let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── Tabs ──
function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
    document.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

// ── Filter Items ──
function filterItems() {
    let search = document.getElementById('itemSearch').value.toLowerCase();
    let condition = document.getElementById('itemCondition').value;
    document.querySelectorAll('#itemsBody tr[data-seller]').forEach(function (row) {
        let matchSearch = !search || row.dataset.seller.includes(search) || row.dataset.material.includes(search);
        let matchCondition = !condition || row.dataset.condition === condition;
        row.style.display = matchSearch && matchCondition ? '' : 'none';
    });
}

// ── Filter Users ──
function filterUsers() {
    let search = document.getElementById('userSearch').value.toLowerCase();
    let role = document.getElementById('userRole').value;
    document.querySelectorAll('#usersBody tr[data-name]').forEach(function (row) {
        let matchSearch = !search || row.dataset.name.includes(search) || row.dataset.email.includes(search);
        let matchRole = !role || row.dataset.role === role;
        row.style.display = matchSearch && matchRole ? '' : 'none';
    });
}

// ── Filter Transactions ──
function filterTx() {
    let search = document.getElementById('txSearch').value.toLowerCase();
    let status = document.getElementById('txStatus').value;
    document.querySelectorAll('#txBody tr[data-buyer]').forEach(function (row) {
        let matchSearch = !search || row.dataset.buyer.includes(search) || row.dataset.seller.includes(search);
        let matchStatus = !status || row.dataset.status === status;
        row.style.display = matchSearch && matchStatus ? '' : 'none';
    });
}

// ── Delete Item ──
function deleteItem(id, btn) {
    if (!confirm('Delete this item?')) return;
    console.log(id, csrfToken);
    $.ajax({
        url: `/admin/items/${id}/delete`,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'POST' },
        success: function (data) {
            btn.closest('tr').remove();
            showToast('✅ Item deleted');
        },
        error: function () { showToast('❌ Failed', '#dc3545'); }
    });
}

// ── Delete User ──
function deleteUser(id, btn) {
    if (!confirm('Delete this user?')) return;
    $.ajax({
        url: `/admin/users/${id}/delete`,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'POST' },
        success: function (data) {
            btn.closest('tr').remove();
            location.reload();
            setTimeout(() => {
                showToast('✅ User deleted');
            }, 1000);
        },
        error: function (data) {
            console.log(data);
            showToast('❌ Failed', '#dc3545');
        }
    });
}

// ── Edit User Modal ──
function openEditUser(id, name, email, phone, role) {
    document.getElementById('editUserId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPhone').value = phone;
    document.getElementById('editRole').value = role;
    document.getElementById('editModal').classList.add('open');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}

// ── Save User ──
function saveUser() {
    var id = document.getElementById('editUserId').value;
    $.ajax({
        url: '/admin/users/' + id,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        data: {
            name: document.getElementById('editName').value,
            email: document.getElementById('editEmail').value,
            phone: document.getElementById('editPhone').value,
            role: document.getElementById('editRole').value,
        },
        success: function () {
            closeEditModal();
            showToast('✅ User updated');
            setTimeout(function () { location.reload(); }, 1000);
        },
        error: function () { showToast('❌ Failed', '#dc3545'); }
    });
}

// ── Toast ──
function showToast(msg, color) {
    let t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = color || '#198754';
    t.classList.add('show');
    setTimeout(function () { t.classList.remove('show'); }, 2500);
}