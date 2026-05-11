"use strict";
// =====================
// CURRENCY
// =====================
let currency = localStorage.getItem("currency") || "EGP";
let USD_RATE = 50;
let allItems = [];
let requestedIds = [];
let swappedIds = [];
let userRole = "";
let container = document.getElementById("cardsContainer");
let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// =====================
// LOAD DATA
// =====================
function loadItems(callback) {
    if (!isLoggedIn) {
        allItems = [];
        if (callback) callback();
        return;
    }

    $.ajax({
        url: '/items/my-actions',
        method: 'GET',
        success: function (data) {
            requestedIds = data.requested || [];
            swappedIds = data.swapped || [];

            $.ajax({
                url: '/items/all',
                method: 'GET',
                success: function (res) {
                    userRole = res.role;
                    allItems = res.items;
                    render();
                    if (callback) callback();
                },
                error: function () {
                    showToast('❌ Failed to load items', '#dc3545');
                }
            });
        },
        error: function () {
            $.ajax({
                url: '/items/all',
                method: 'GET',
                success: function (res) {
                    userRole = res.role;
                    allItems = res.items;
                    render();
                    if (callback) callback();
                }
            });
        }
    });
}

// =====================
// BUILD ACTION BUTTONS
// =====================
function buildActionBtns(item) {
    let isRequested = requestedIds.indexOf(item.item_id) !== -1;
    let isSwapped = swappedIds.indexOf(item.item_id) !== -1;

    if (userRole === 'admin') {
        return '';

    } else if (userRole === 'seller' && item.is_mine) {
        return `
            <div class="btns">
                <button class="btn-edit-item"
                    onclick="openEditItem(${item.item_id}, ${item.price}, ${item.condition_id}, ${item.material_id})">
                    ✏️ Edit
                </button>
                <button class="btn-delete-item"
                    onclick="deleteMyItem(${item.item_id}, this)">
                    🗑️ Delete
                </button>
            </div>`;

    } else if (userRole !== 'seller') {
        return `
            <div class="btns">
                <button class="swap-btn ${isSwapped ? 'requested' : ''}"
                    ${isSwapped ? 'disabled' : `onclick="openSwapModal(${item.item_id}, ${item.owner_id})"`}>
                    ${isSwapped ? '🔄 Swapped' : 'Swap'}
                </button>
                <button class="request-btn ${isRequested ? 'requested' : ''}"
                    ${isRequested ? 'disabled' : `onclick="sendRequest(${item.item_id}, this)"`}>
                    ${isRequested ? '⏳ Requested' : '📋 Request'}
                </button>
            </div>`;
    }

    return '';
}

// =====================
// RENDER CARDS
// =====================
function render() {
    if (!container) return;
    container.innerHTML = "";

    if (!allItems.length) {
        container.innerHTML =
            (userRole === 'seller')
                ? `<div style="text-align:center;padding:60px;grid-column:1/-1;">
                    <p style="color:#aaa;font-size:16px;margin-bottom:16px;">You have no items yet</p>
                    <a href="${addItemUrl}" style="background:#198754;color:#fff;padding:10px 24px;border-radius:10px;text-decoration:none;font-weight:600;">
                    + Add your first item
                    </a>
                </div>`
                : `<p style="text-align:center;padding:40px;color:#aaa;grid-column:1/-1;">No items found</p>`;
        return;
    }

    allItems.forEach(function (item, i) {
        let card = document.createElement("div");
        card.className = "card";
        card.setAttribute("data-aos", "fade-up");
        card.setAttribute("data-aos-delay", String(Math.min(i * 100, 800)));
        card.setAttribute("data-aos-duration", "500");

        card.innerHTML = `
            <img src="${item.image}" class="card-img" style="cursor:pointer;"
                onerror="this.src='/assets/shared/images/default.png'">
            <div class="card-body">
                <p class="card-owner"><i class="fas fa-user"></i> ${item.owner_name}</p>
                <p>Category: ${item.category}</p>
                <p>Condition: ${item.condition}</p>
                <p>Material: ${item.material}</p>
                <p>Price: ${convertPrice(item.price)}</p>
                ${buildActionBtns(item)}
            </div>`;

        container.appendChild(card);
    });

    if (typeof AOS !== "undefined") AOS.refresh();
}

// =====================
// SWAP MODAL
// =====================
function openSwapModal(itemId, ownerId) {
    let modal = document.getElementById("swapModal");
    if (!modal) return;
    modal.style.display = "flex";
    modal.setAttribute("data-item-id", itemId);
    document.getElementById("offerInput").value = "";
    document.querySelector("p.swap.alert").classList.add("d-none");
}

function sendSwapOffer() {
    let offer = document.getElementById("offerInput");
    let modal = document.getElementById("swapModal");
    let sendBtn = document.getElementById("sendOffer");

    if (!offer.value || +(offer.value) <= 0) {
        document.querySelector("p.swap.alert").classList.remove("d-none");
        return;
    }
    document.querySelector("p.swap.alert").classList.add("d-none");
    let itemId = modal.getAttribute("data-item-id");
    sendBtn.disabled = true;
    sendBtn.textContent = "Sending...";
    $.ajax({
        url: '/swap/store',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        data: { requested_item_id: itemId, cash_topup_amount: offer.value },
        success: function () {
            swappedIds.push(parseInt(itemId));
            showToast('✔ Swap request sent!', '#198754');
            closeSwap();
            render();
        },
        error: function () {
            showToast('❌ Failed to send swap', '#dc3545');
        },
        complete: function () {
            sendBtn.disabled = false;
            sendBtn.textContent = "Send";
        }
    });
}

function closeSwap() {
    let modal = document.getElementById("swapModal");
    if (modal) modal.style.display = "none";
}

window.openSwapModal = openSwapModal;
window.sendSwapOffer = sendSwapOffer;
window.closeSwap = closeSwap;

// =====================
// REQUEST
// =====================
function sendRequest(itemId, btn) {
    btn.disabled = true;
    btn.textContent = "⏳ Requested";
    btn.classList.add("requested");

    $.ajax({
        url: '/request/store',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        data: { item_id: itemId },
        success: function () {
            requestedIds.push(parseInt(itemId));
            showToast('✔ Request sent!', '#198754');
        },
        error: function () {
            btn.disabled = false;
            btn.textContent = "📋 Request";
            btn.classList.remove("requested");
            showToast('❌ Failed to send request', '#dc3545');
        }
    });
}
window.sendRequest = sendRequest;

// =====================
// EDIT ITEM MODAL
// =====================
function openEditItem(itemId, price, conditionId, materialId) {
    document.getElementById('editItemId').value = itemId;
    document.getElementById('editPrice').value = price;
    document.getElementById('editCondition').value = conditionId;
    document.getElementById('editMaterial').value = materialId;
    document.getElementById('editImage').value = '';
    document.getElementById('editItemModal').style.display = 'flex';
}

function closeEditItem() {
    document.getElementById('editItemModal').style.display = 'none';
}

function saveEditItem() {
    let id = document.getElementById('editItemId').value;
    let price = document.getElementById('editPrice').value;
    let condition = document.getElementById('editCondition').value;
    let material = document.getElementById('editMaterial').value;
    let imageFile = document.getElementById('editImage').files[0];

    if (!price || parseFloat(price) < 1) {
        showToast('❌ Price must be at least 1 EGP', '#dc3545');
        return;
    }

    let formData = new FormData();
    formData.append('price', price);
    formData.append('condition_id', condition);
    formData.append('material_id', material);
    if (imageFile) formData.append('image', imageFile);

    $.ajax({
        url: `/items/${id}/update`,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
            showToast('✅ Item updated!', '#198754');
            closeEditItem();
            loadItems();
        },
        error: function () {
            showToast('❌ Failed to update', '#dc3545');
        }
    });
}

window.openEditItem = openEditItem;
window.closeEditItem = closeEditItem;
window.saveEditItem = saveEditItem;

// =====================
// SEARCH
// =====================
function searchItems() {
    let value = document.getElementById("searchInput").value.toLowerCase().trim();

    if (!value) { render(); return; }

    let filtered = allItems.filter(function (item) {
        return (item.material || "").toLowerCase().includes(value) ||
            (item.condition || "").toLowerCase().includes(value) ||
            (item.owner_name || "").toLowerCase().includes(value);
    });

    container.innerHTML = "";

    if (!filtered.length) {
        container.innerHTML = `<p style="text-align:center;padding:40px;color:#aaa;grid-column:1/-1;">No items found for "${value}"</p>`;
        return;
    }

    filtered.forEach(function (item) {
        container.innerHTML += `
            <div class="card">
                <img src="${item.image}" class="card-img"
                    onerror="this.src='/assets/shared/images/default.png'">
                <div class="card-body">
                    <p class="card-owner"><i class="fas fa-user"></i> ${item.owner_name}</p>
                    <p>Category: ${item.category}</p>
                    <p>Condition: ${item.condition}</p>
                    <p>Material: ${item.material}</p>
                    <p>Price: ${convertPrice(item.price)}</p>
                    ${buildActionBtns(item)}
                </div>
            </div>`;
    });
}
window.searchItems = searchItems;

function deleteMyItem(itemId, btn) {
    if (!confirm('Delete this item?')) return;

    $.ajax({
        url: `/items/${itemId}/delete`,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        success: function () {
            btn.closest('.card').remove();
            showToast('✅ Item deleted!', '#198754');
            if (!allItems.length) {
                container.innerHTML =
                    (userRole === 'seller') ?
                    `<div style="text-align:center;padding:60px;grid-column:1/-1;">
                        <p style="color:#aaa;font-size:16px;margin-bottom:16px;">You have no items yet</p>
                        <a href="${addItemUrl}" style="background:#198754;color:#fff;padding:10px 24px;border-radius:10px;text-decoration:none;font-weight:600;">
                            + Add your first item
                        </a>
                    </div>`
                    : `<p style="text-align:center;padding:40px;color:#aaa;grid-column:1/-1;">No items found</p>`;
            }
        },
        error: function () {
            showToast('❌ Failed to delete', '#dc3545');
        }
    });
}
window.deleteMyItem = deleteMyItem;

// =====================
// UTILITIES
// =====================
function showToast(message, color) {
    let toast = document.getElementById("toast");
    if (!toast) return;
    toast.innerText = message;
    toast.style.background = color || "#198754";
    toast.classList.add("show");
    setTimeout(function () { toast.classList.remove("show"); }, 2000);
}

function convertPrice(price) {
    let p = parseFloat(price) || 0;
    if (currency === "EGP") return `${p} EGP`;
    return `$${(p / USD_RATE).toFixed(2)}`;
}

function toggleCurrency() {
    let switcher = document.querySelector(".currency-switch");
    if (switcher) switcher.classList.toggle("active");
    currency = currency === "EGP" ? "USD" : "EGP";
    localStorage.setItem("currency", currency);
    render();
}
window.toggleCurrency = toggleCurrency;

// =====================
// INIT
// =====================
document.addEventListener("DOMContentLoaded", function () {
    loadItems();

    let sendBtn = document.getElementById("sendOffer");
    let closeBtn = document.getElementById("closeModal");
    if (sendBtn) sendBtn.addEventListener("click", sendSwapOffer);
    if (closeBtn) closeBtn.addEventListener("click", closeSwap);
});