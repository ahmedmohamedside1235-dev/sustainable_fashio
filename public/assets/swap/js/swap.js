"use strict";

var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var allSwaps = [];
var userRole = "";

function loadSwaps() {
    $.ajax({
        url: '/swaps/all',
        method: 'GET',
        success: function (data) {
            userRole = data.role;
            allSwaps = data.swaps;
            renderSwaps();
        },
        error: function () {
            showToast('❌ Failed to load swaps', '#dc3545');
        }
    });
}

function updateStats() {
    var total = allSwaps.length;
    var pending = allSwaps.filter(function (s) { return s.status === 'pending'; }).length;
    var accepted = allSwaps.filter(function (s) { return s.status === 'accepted'; }).length;
    var rejected = allSwaps.filter(function (s) { return s.status === 'rejected'; }).length;

    document.getElementById('statTotal').textContent = total;
    document.getElementById('statPending').textContent = pending;
    document.getElementById('statAccepted').textContent = accepted;
    document.getElementById('statRejected').textContent = rejected;
}

function renderSwaps() {
    var search = (document.getElementById('filterSearch').value || '').toLowerCase();
    var statusFlt = document.getElementById('filterStatus').value;

    updateStats();

    var filtered = allSwaps.filter(function (s) {
        var matchSearch = !search ||
            (s.item && s.item.condition && s.item.condition.toLowerCase().includes(search)) ||
            (s.other_name && s.other_name.toLowerCase().includes(search));
        var matchStatus = !statusFlt || s.status === statusFlt;
        return matchSearch && matchStatus;
    });

    var grid = document.getElementById('swapGrid');
    grid.innerHTML = '';

    if (!filtered.length) {
        grid.innerHTML =
            '<div class="empty-state">' +
            '<i class="fas fa-exchange-alt d-block"></i>' +
            '<h3>No swaps yet</h3>' +
            '</div>';
        return;
    }

    filtered.forEach(function (s, idx) {
        var item = s.item || {};
        var isPending = s.status === 'pending';
        var icons = { pending: '⏳', accepted: '✅', rejected: '❌' };
        var icon = icons[s.status] || '⏳';

        var actionBtns = '';
        if (userRole === 'seller' && isPending) {
            actionBtns =
                '<button class="btn-accept" onclick="handleAccept(' + s.swap_id + ', this)">' +
                '<i class="fas fa-check me-1"></i>Accept</button>' +
                '<button class="btn-reject" onclick="handleReject(' + s.swap_id + ', this)">' +
                '<i class="fas fa-times me-1"></i>Reject</button>';
        } else if (userRole === 'buyer' && isPending) {
            actionBtns =
                '<button class="btn-cancel" onclick="handleCancel(' + s.swap_id + ', this)">' +
                '<i class="fas fa-times me-1"></i>Cancel</button>';
        } else {
            actionBtns = '<div class="btn-disabled">' + icon + ' ' + s.status + '</div>';
        }

        var card = document.createElement('div');
        card.className = 'swap-card';
        card.setAttribute('data-aos', 'fade-up');
        card.setAttribute('data-aos-delay', Math.min(idx * 80, 500));

        card.innerHTML =
            '<img class="swap-card-img" src="' + (item.image || '/assets/shared/images/default.png') + '" alt="">' +
            '<span class="status-badge ' + s.status + '">' + icon + ' ' + s.status + '</span>' +
            '<span class="swap-id-badge">#' + s.swap_id + '</span>' +
            '<div class="swap-body">' +

            '<p class="card-owner"><i class="fas fa-user me-1"></i>' +
            (userRole === 'seller' ? 'From: ' : 'Seller: ') + s.other_name + '</p>' +

            '<div class="swap-info-row">' +
            '<span class="label">Category</span>' +
            '<span class="value">' + (item.category || '—') + '</span>' +
            '</div>' +

            '<div class="swap-info-row">' +
            '<span class="label">Condition</span>' +
            '<span class="value">' + (item.condition || '—') + '</span>' +
            '</div>' +

            '<div class="swap-info-row">' +
            '<span class="label">Material</span>' +
            '<span class="value">' + (item.material || '—') + '</span>' +
            '</div>' +

            '<div class="swap-info-row">' +
            '<span class="label">Item Price</span>' +
            '<span class="value green">' + (item.price || '—') + ' EGP</span>' +
            '</div>' +

            '<div class="offer-box">' +
            '<div class="offer-label">💰 Cash Top-up Offered</div>' +
            s.cash_topup_amount + ' EGP' +
            '</div>' +

            '<div class="req-footer">' +
            '<span class="req-date"><i class="fas fa-calendar-alt me-1"></i>' + s.created_at + '</span>' +
            '</div>' +

            '<div class="action-btns" id="actions-' + s.swap_id + '">' + actionBtns + '</div>' +
            '</div>';

        grid.appendChild(card);
    });

    if (typeof AOS !== 'undefined') AOS.refresh();
}


function handleAccept(swapId, btn) {
    if (!confirm('Accept this swap?')) return;

    $.ajax({
        url: '/swap/' + swapId + '/update',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'PATCH' },
        data: { status: 'accepted' },
        success: function () {
            updateCardStatus(swapId, 'accepted');
            showToast('✅ Swap accepted!', '#198754');
        },
        error: function () { showToast('❌ Failed', '#dc3545'); }
    });
}


function handleReject(swapId, btn) {
    if (!confirm('Reject this swap?')) return;

    $.ajax({
        url: '/swap/' + swapId + '/update',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'PATCH' },
        data: { status: 'rejected' },
        success: function () {
            updateCardStatus(swapId, 'rejected');
            showToast('❌ Swap rejected', '#e74c3c');
        },
        error: function () { showToast('❌ Failed', '#dc3545'); }
    });
}

function handleCancel(swapId, btn) {
    if (!confirm('Cancel this swap?')) return;

    $.ajax({
        url: '/swap/' + swapId + '/cancel',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'PATCH' },
        success: function () {
            updateCardStatus(swapId, 'rejected');
            showToast('Swap cancelled', '#f0a500');
        },
        error: function () { showToast('❌ Failed', '#dc3545'); }
    });
}

function updateCardStatus(swapId, newStatus) {
    allSwaps = allSwaps.map(function (s) {
        if (s.swap_id === swapId) s.status = newStatus;
        return s;
    });

    var actions = document.getElementById('actions-' + swapId);
    var card = actions ? actions.closest('.swap-card') : null;
    var badge = card ? card.querySelector('.status-badge') : null;
    var icons = { accepted: '✅', rejected: '❌', pending: '⏳' };

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
    var t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = color || '#2C5F2D';
    t.classList.add('show');
    setTimeout(function () { t.classList.remove('show'); }, 2500);
}

// =====================
// Init
// =====================
document.addEventListener('DOMContentLoaded', function () {
    loadSwaps();
});

window.handleAccept = handleAccept;
window.handleReject = handleReject;
window.handleCancel = handleCancel;
window.renderSwaps = renderSwaps;