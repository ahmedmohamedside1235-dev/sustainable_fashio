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
let searchTimer;
function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(filterUsers, 400);
}

function filterUsers() {
    let search = document.getElementById('userSearch').value.toLowerCase();
    let role = document.getElementById('userRole').value;

    $.ajax({
        url: '/admin/users/search',
        method: 'GET',
        data: { query: search, role: role },
        success: function (users) {
            let tbody = document.getElementById('usersBody');
            tbody.innerHTML = '';

            if (!users.length) {
                tbody.innerHTML = '<tr class="empty-row"><td colspan="7">No users found</td></tr>';
                return;
            }

            users.forEach(function (u, i) {
                let roleClass = u.role === 'admin' ? 'badge-purple' : (u.role === 'seller' ? 'badge-orange' : 'badge-blue');

                // الـ admin اللي لوجن مش بيشوف أزرار على نفسه
                let actions = u.user_id === currentAdminId ? '' : `
                    <button class="btn-edit" onclick="openEditUser(${u.user_id}, '${u.name}', '${u.email}', '${u.phone}', '${u.role}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-delete" onclick="deleteUser(${u.user_id}, this)">
                        <i class="fas fa-trash"></i>
                    </button>`;

                tbody.innerHTML += `
                    <tr data-name="${u.name.toLowerCase()}" data-email="${u.email.toLowerCase()}" data-role="${u.role}">
                        <td style="color:#aaa;">${i + 1}</td>
                        <td><strong>${u.name}</strong></td>
                        <td style="color:#666;">${u.email}</td>
                        <td style="color:#666;">${u.phone}</td>
                        <td><span class="badge-pill ${roleClass}">${u.role}</span></td>
                        <td style="color:#aaa;font-size:12px;">${u.created_at}</td>
                        <td>${actions}</td>
                    </tr>`;
            });
        }
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
    $.ajax({
        url: `/admin/items/${id}/delete`,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        success: function (data) {
            btn.closest('tr').remove();
            showToast('✅ Item deleted');
            setTimeout(() => { location.reload(); }, 300);
        },
        error: function (error) {
            console.log(error);
            showToast('❌ Failed', '#dc3545');
        }
    });
}

// ── Delete User ──
function deleteUser(id, btn) {
    if (!confirm('Delete this user?')) return;
    $.ajax({
        url: `/admin/users/${id}/delete`,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        success: function (data) {
            btn.closest('tr').remove();
            location.reload();
            setTimeout(() => { showToast('✅ User deleted'); }, 1000);
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
        success: function (data) {
            closeEditModal();
            showToast('✅ User updated');
            setTimeout(function () { location.reload(); }, 1000);
        },
        error: function (error) {
            showToast('❌ Failed', '#dc3545');
            setTimeout(() => { showToast('❌ The email has already been taken', '#dc3545'); }, 700);
        }
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