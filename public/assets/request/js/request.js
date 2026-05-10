"use strict";

let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let allTransactions = [];
let userRole = "";

function loadRequests() {
    $.ajax({
        url: '/requests/all',
        method: 'GET',
        success: function (data) {
            userRole = data.role;
            allTransactions = data.transactions;
            renderRequests();
        },
        error: function () {
            showToast('❌ Failed to load requests', '#dc3545');
        }
    });
}

function updateStats() {
    let total = allTransactions.length;
    let pending = allTransactions.filter(function (t) { return t.status === 'pending'; }).length;
    let accepted = allTransactions.filter(function (t) { return t.status === 'accepted'; }).length;
    let rejected = allTransactions.filter(function (t) { return t.status === 'rejected'; }).length;

    document.getElementById('statTotal').textContent = total;
    document.getElementById('statPending').textContent = pending;
    document.getElementById('statAccepted').textContent = accepted;
    document.getElementById('statRejected').textContent = rejected;
}

function renderRequests() {
    let search = (document.getElementById('filterSearch').value || '').toLowerCase();
    let statusFlt = document.getElementById('filterStatus').value;
    updateStats();
    let filtered = allTransactions.filter(function (t) {
        let matchSearch = !search ||
            (t.item && t.item.condition && t.item.condition.toLowerCase().includes(search)) ||
            (t.other_name && t.other_name.toLowerCase().includes(search));
        let matchStatus = !statusFlt || t.status === statusFlt;
        return matchSearch && matchStatus;
    });

    let grid = document.getElementById('reqGrid');
    grid.innerHTML = '';

    if (!filtered.length) {
        grid.innerHTML =
            '<div class="empty-state">' +
            '<i class="fas fa-box-open d-block"></i>' +
            '<h3>No requests yet</h3>' +
            '</div>';
        return;
    }

    filtered.forEach(function (t, idx) {
        let item = t.item || {};
        let isPending = t.status === 'pending';

        let statusIcons = { pending: '⏳', accepted: '✅', rejected: '❌' };
        let statusIcon = statusIcons[t.status] || '⏳';

        let card = document.createElement('div');
        card.className = 'req-card';
        card.setAttribute('data-aos', 'fade-up');
        card.setAttribute('data-aos-delay', Math.min(idx * 80, 500));

        let actionBtns = '';

        if (userRole === 'seller' && isPending) {
            actionBtns =
                '<button class="btn-accept" onclick="handleAccept(' + t.transaction_id + ', this)">' +
                '<i class="fas fa-check me-1"></i>Accept</button>' +
                '<button class="btn-reject" onclick="handleReject(' + t.transaction_id + ', this)">' +
                '<i class="fas fa-times me-1"></i>Reject</button>';
        } else if (userRole === 'buyer' && isPending) {
            actionBtns =
                '<button class="btn-cancel" onclick="handleCancel(' + t.transaction_id + ', this)">' +
                '<i class="fas fa-times me-1"></i>Cancel</button>';
        } else {
            actionBtns = '<div class="btn-disabled">' + statusIcon + ' ' + t.status + '</div>';
        }

        card.innerHTML =
            '<img class="req-card-img" src="' + (item.image || '/assets/shared/images/default.png') + '" alt="">' +
            '<span class="status-badge ' + t.status + '">' + statusIcon + ' ' + t.status + '</span>' +
            '<span class="txn-badge">#' + t.transaction_id + '</span>' +
            '<div class="req-body">' +

            // لو seller يعرض اسم الـ buyer والعكس
            '<p class="card-owner"><i class="fas fa-user me-1"></i>' +
            (userRole === 'seller' ? 'From: ' : 'Seller: ') + t.other_name + '</p>' +

            '<div class="req-meta">' +
            (item.category ? '<span class="req-tag">✨ ' + item.category + '</span>' : '') +
            (item.condition ? '<span class="req-tag">✨ ' + item.condition + '</span>' : '') +
            (item.material ? '<span class="req-tag">🧵 ' + item.material + '</span>' : '') +
            (item.price_at_purchase ? '<span class="req-tag price">💰 ' + item.price_at_purchase + ' EGP</span>' : '') +
            '</div>' +

            '<div class="req-footer">' +
            '<span class="req-date"><i class="fas fa-calendar-alt me-1"></i>' + t.created_at + '</span>' +
            '</div>' +

            '<div class="action-btns" id="actions-' + t.transaction_id + '">' + actionBtns + '</div>' +
            '</div>';

        grid.appendChild(card);
    });

    if (typeof AOS !== 'undefined') AOS.refresh();
}

function handleAccept(transactionId, btn) {
    if (!confirm('Accept this request?')) return;

    $.ajax({
        url: '/requests/' + transactionId + '/update',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'PATCH' },
        data: { status: 'accepted' },
        success: function () {
            updateCardStatus(transactionId, 'accepted');
            showToast('✅ Request accepted!', '#198754');
        },
        error: function () { showToast('❌ Failed', '#dc3545'); }
    });
}

function handleReject(transactionId, btn) {
    if (!confirm('Reject this request?')) return;

    $.ajax({
        url: '/requests/' + transactionId + '/update',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'PATCH' },
        data: { status: 'rejected' },
        success: function () {
            updateCardStatus(transactionId, 'rejected');
            showToast('❌ Request rejected', '#e74c3c');
        },
        error: function () { showToast('❌ Failed', '#dc3545'); }
    });
}

function handleCancel(transactionId, btn) {
    if (!confirm('Cancel this request?')) return;

    $.ajax({
        url: '/requests/' + transactionId + '/cancel',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'PATCH' },
        success: function () {
            updateCardStatus(transactionId, 'rejected');
            showToast('Request cancelled', '#f0a500');
        },
        error: function () { showToast('❌ Failed', '#dc3545'); }
    });
}

function updateCardStatus(transactionId, newStatus) {
    allTransactions = allTransactions.map(function (t) {
        if (t.transaction_id === transactionId) t.status = newStatus;
        return t;
    });

    let actions = document.getElementById('actions-' + transactionId);
    let card = actions ? actions.closest('.req-card') : null;
    let badge = card ? card.querySelector('.status-badge') : null;

    let icons = { accepted: '✅', rejected: '❌', pending: '⏳' };

    if (badge) {
        badge.className = 'status-badge ' + newStatus;
        badge.textContent = icons[newStatus] + ' ' + newStatus;
    }
    if (actions) {
        actions.innerHTML = '<div class="btn-disabled">' + icons[newStatus] + ' ' + newStatus + '</div>';
    }

    updateStats();
}

// =====================
// Utilities
// =====================
function showToast(msg, color) {
    let t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = color || '#2C5F2D';
    t.classList.add('show');
    setTimeout(function () { t.classList.remove('show'); }, 2500);
}

// =====================
// Init
// =====================
document.addEventListener('DOMContentLoaded', function () {
    loadRequests();
});

window.handleAccept = handleAccept;
window.handleReject = handleReject;
window.handleCancel = handleCancel;
window.renderRequests = renderRequests;