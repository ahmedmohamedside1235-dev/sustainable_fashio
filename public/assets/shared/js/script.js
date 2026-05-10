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
    if ((!isLoggedIn) ?? undefined) {
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
// RENDER CARDS
// =====================
function render() {
    if (!container) return;
    container.innerHTML = "";

    if (!allItems.length) {
        container.innerHTML = '<p style="text-align:center;padding:40px;color:#aaa;grid-column:1/-1;">No items found</p>';
        return;
    }

    allItems.forEach(function (item, i) {
        let isRequested = requestedIds.indexOf(item.item_id) !== -1;
        let isSwapped = swappedIds.indexOf(item.item_id) !== -1;

        let card = document.createElement("div");
        card.className = "card";
        card.setAttribute("data-aos", "fade-up");
        card.setAttribute("data-aos-delay", String(Math.min(i * 100, 800)));
        card.setAttribute("data-aos-duration", "500");
        let actionBtns = '';
        if (userRole !== 'seller') {
            actionBtns =
                '<div class="btns">' +
                '<button class="swap-btn' + (isSwapped ? ' requested' : '') + '" ' +
                (isSwapped ? 'disabled' : 'onclick="openSwapModal(' + item.item_id + ', ' + item.owner_id + ')"') +
                '>' + (isSwapped ? '🔄 Swapped' : 'Swap') + '</button>' +
                '<button class="request-btn' + (isRequested ? ' requested' : '') + '" ' +
                (isRequested ? 'disabled' : 'onclick="sendRequest(' + item.item_id + ', this)"') +
                '>' + (isRequested ? '⏳ Requested' : '📋 Request') + '</button>' +
                '</div>';
        }

        card.innerHTML =
            '<img src="' + item.image + '" class="card-img" style="cursor:pointer;" onerror="this.src=\'/assets/shared/images/default.png\'">' +
            '<div class="card-body">' +
            '<p class="card-owner"><i class="fas fa-user"></i> ' + item.owner_name + '</p>' +
            '<p>category : ' + item.category + '</p>' +
            '<p>Condition : ' + item.condition + '</p>' +
            '<p>Material : ' + item.material + '</p>' +
            '<p>Price: ' + convertPrice(item.price) + '</p>' +
            actionBtns +
            '</div>';
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

    if (!offer.value || +(offer.value) <= 0) {
        document.querySelector("p.swap.alert").classList.remove("d-none");
        return;
    }
    document.querySelector("p.swap.alert").classList.add("d-none");

    let itemId = modal.getAttribute("data-item-id");

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
        container.innerHTML = '<p style="text-align:center;padding:40px;color:#aaa;grid-column:1/-1;">No items found for "' + value + '"</p>';
        return;
    }

    filtered.forEach(function (item) {
        let isRequested = requestedIds.indexOf(item.item_id) !== -1;
        let isSwapped = swappedIds.indexOf(item.item_id) !== -1;

        let actionBtns = '';
        if (userRole !== 'seller') {
            actionBtns =
                '<div class="btns">' +
                '<button class="swap-btn' + (isSwapped ? ' requested' : '') + '" ' +
                (isSwapped ? 'disabled' : 'onclick="openSwapModal(' + item.item_id + ', ' + item.owner_id + ')"') +
                '>' + (isSwapped ? '🔄 Swapped' : 'Swap') + '</button>' +
                '<button class="request-btn' + (isRequested ? ' requested' : '') + '" ' +
                (isRequested ? 'disabled' : 'onclick="sendRequest(' + item.item_id + ', this)"') +
                '>' + (isRequested ? '⏳ Requested' : '📋 Request') + '</button>' +
                '</div>';
        }

        container.innerHTML +=
            '<div class="card">' +
            '<img src="' + item.image + '" class="card-img" onerror="this.src=\'/assets/shared/images/default.png\'">' +
            '<div class="card-body">' +
            '<p class="card-owner"><i class="fas fa-user"></i> ' + item.owner_name + '</p>' +
            '<p>category : ' + item.category + '</p>' +
            '<p>Condition: ' + item.condition + '</p>' +
            '<p>Material: ' + item.material + '</p>' +
            '<p>Price: ' + convertPrice(item.price) + '</p>' +
            actionBtns +
            '</div>' +
            '</div>';
    });
}
window.searchItems = searchItems;

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
    if (currency === "EGP") return p + " EGP";
    return "$" + (p / USD_RATE).toFixed(2);
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