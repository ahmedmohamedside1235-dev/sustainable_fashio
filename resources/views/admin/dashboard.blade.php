<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Control Center – SwapSustain</title>
    <link rel="icon" href="{{ asset('assets/shared/images/recycle-shirt.png') }}">
    <link href="{{ asset('assets/shared/css/plugins/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin.css') }}">
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex" href="{{ route('home') }}">
                <div class="brand-icon me-2">♻</div>
                SwapSustain
            </a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('collections') }}">
                            <i class="fas fa-tshirt me-1"></i>Collections</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-shield-alt me-1"></i>Dashboard</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown"
                            role="button">
                            <i class="fas fa-user-shield me-1"></i>
                            {{ Auth::guard('user')->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>Add Admin</a></li>
                            <li><a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HEADER -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-shield-alt me-2"></i>Control Center</h1>
            <p>Monitor and manage everything on SwapSustain</p>
        </div>
    </div>

    <!-- TABS -->
    <div class="tabs-bar">
        <button class="tab-btn active" onclick="switchTab('overview', this)">
            <i class="fas fa-chart-pie me-1"></i>Overview
        </button>
        <button class="tab-btn" onclick="switchTab('items', this)">
            <i class="fas fa-tshirt me-1"></i>Items
        </button>
        <button class="tab-btn" onclick="switchTab('users', this)">
            <i class="fas fa-users me-1"></i>Users
        </button>
        <button class="tab-btn" onclick="switchTab('transactions', this)">
            <i class="fas fa-exchange-alt me-1"></i>Transactions
        </button>
        <button class="tab-btn" onclick="switchTab('reports', this)">
            <i class="fas fa-leaf me-1"></i>Sustainability
        </button>
    </div>

    {{-- ═══════════════ TAB: OVERVIEW ═══════════════ --}}
    <div id="tab-overview" class="tab-panel active">
        <div class="section">

            <div class="stats-row">
                <div class="stat-card">
                    <h2>{{ $stats['total_users'] }}</h2>
                    <p><i class="fas fa-users me-1"></i>Total Users</p>
                </div>
                <div class="stat-card blue">
                    <h2>{{ $stats['total_items'] }}</h2>
                    <p><i class="fas fa-tshirt me-1"></i>Total Items</p>
                </div>
                <div class="stat-card orange">
                    <h2>{{ $stats['total_transactions'] }}</h2>
                    <p><i class="fas fa-exchange-alt me-1"></i>Transactions</p>
                </div>
                <div class="stat-card purple">
                    <h2>{{ $stats['total_swaps'] }}</h2>
                    <p><i class="fas fa-sync me-1"></i>Swap Requests</p>
                </div>
                <div class="stat-card red">
                    <h2>{{ $stats['pending_transactions'] }}</h2>
                    <p><i class="fas fa-clock me-1"></i>Pending</p>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;flex-wrap:wrap;">

                <!-- Recent Transactions -->
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:#333;margin-bottom:12px;">
                        <i class="fas fa-history me-2" style="color:#198754;"></i>Recent Transactions
                    </h3>
                    <div class="activity-list">
                        @forelse($recentTransactions as $t)
                            <div class="activity-item">
                                <div
                                    class="activity-dot {{ $t->status === 'accepted' ? 'dot-green' : ($t->status === 'rejected' ? 'dot-red' : 'dot-orange') }}">
                                </div>
                                <div class="activity-text">
                                    <strong>{{ $t->buyer->name }}</strong> requested from
                                    <strong>{{ $t->seller->name }}</strong>
                                </div>
                                <span
                                    class="badge-pill {{ $t->status === 'accepted' ? 'badge-green' : ($t->status === 'rejected' ? 'badge-red' : 'badge-orange') }}">
                                    {{ $t->status }}
                                </span>
                                <span class="activity-time">{{ $t->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p style="text-align:center;color:#aaa;padding:20px;">No transactions yet</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Swaps -->
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:#333;margin-bottom:12px;">
                        <i class="fas fa-sync me-2" style="color:#198754;"></i>Recent Swaps
                    </h3>
                    <div class="activity-list">
                        @forelse($recentSwaps as $s)
                            <div class="activity-item">
                                <div
                                    class="activity-dot {{ $s->status === 'accepted' ? 'dot-green' : ($s->status === 'rejected' ? 'dot-red' : 'dot-orange') }}">
                                </div>
                                <div class="activity-text">
                                    <strong>{{ $s->requester->name }}</strong> →
                                    <strong>{{ $s->receiver->name }}</strong>
                                    <span style="color:#198754;font-weight:700;"> +{{ $s->cash_topup_amount }}
                                        EGP</span>
                                </div>
                                <span
                                    class="badge-pill {{ $s->status === 'accepted' ? 'badge-green' : ($s->status === 'rejected' ? 'badge-red' : 'badge-orange') }}">
                                    {{ $s->status }}
                                </span>
                                <span class="activity-time">{{ $s->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p style="text-align:center;color:#aaa;padding:20px;">No swaps yet</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════ TAB: ITEMS ═══════════════ --}}
    <div id="tab-items" class="tab-panel">
        <div class="section">
            <div class="stats-row">
                <div class="stat-card itemss">
                    <h2>{{ $itemStats['total'] }}</h2>
                    <p>Total Items</p>
                </div>
            </div>

            <div class="toolbar">
                <input type="text" id="itemSearch" placeholder="🔍 Search by seller or material…"
                    oninput="filterItems()">
                <select id="itemCondition" onchange="filterItems()">
                    <option value="">All Conditions</option>
                    <option value="new">New</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                </select>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Seller</th>
                            <th>Condition</th>
                            <th>Material</th>
                            <th>Price</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @forelse($items as $i => $item)
                            <tr data-seller="{{ strtolower($item->seller->name) }}"
                                data-material="{{ strtolower($item->material->material_name) }}"
                                data-condition="{{ $item->condition->condition_name }}">
                                <td style="color:#aaa;">{{ $i + 1 }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <img class="item-img" src="{{ asset('storage/uploaded/' . $item->image) }}"
                                            onerror="this.src='/assets/shared/images/default.png'" alt="">
                                        <span style="font-size:12px;color:#aaa;">#{{ $item->item_id }}</span>
                                    </div>
                                </td>
                                <td><span class="badge-pill badge-blue">{{ $item->seller->name }}</span></td>
                                <td><span
                                        class="badge-pill badge-orange">{{ $item->condition->condition_name }}</span>
                                </td>
                                <td><span class="badge-pill badge-green">{{ $item->material->material_name }}</span>
                                </td>
                                <td><span class="price-tag">{{ number_format($item->price, 2) }} EGP</span></td>
                                <td style="color:#aaa;font-size:12px;">{{ $item->created_at->format('d M Y') }}</td>
                                <td>
                                    <button class="btn-delete" onclick="deleteItem({{ $item->item_id }}, this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="8">No items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════ TAB: USERS ═══════════════ --}}
    <div id="tab-users" class="tab-panel">
        <div class="section">
            <div class="stats-row">
                <div class="stat-card">
                    <h2>{{ $userStats['total'] }}</h2>
                    <p>Total Users</p>
                </div>
                <div class="stat-card blue">
                    <h2>{{ $userStats['buyers'] }}</h2>
                    <p>Buyers</p>
                </div>
                <div class="stat-card orange">
                    <h2>{{ $userStats['sellers'] }}</h2>
                    <p>Sellers</p>
                </div>
                <div class="stat-card purple">
                    <h2>{{ $userStats['admins'] }}</h2>
                    <p>Admins</p>
                </div>
            </div>

            <div class="toolbar">
                <input type="text" id="userSearch" placeholder="🔍 Search by name or email…"
                    oninput="filterUsers()">
                <select id="userRole" onchange="filterUsers()">
                    <option value="">All Roles</option>
                    <option value="buyer">Buyer</option>
                    <option value="seller">Seller</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersBody">
                        @forelse($users as $i => $user)
                            <tr data-name="{{ strtolower($user->name) }}"
                                data-email="{{ strtolower($user->email) }}" data-role="{{ $user->role }}">
                                <td style="color:#aaa;">{{ $i + 1 }}</td>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td style="color:#666;">{{ $user->email }}</td>
                                <td style="color:#666;">{{ $user->phone }}</td>
                                <td>
                                    <span
                                        class="badge-pill
                                {{ $user->role === 'admin' ? 'badge-purple' : ($user->role === 'seller' ? 'badge-orange' : 'badge-blue') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td style="color:#aaa;font-size:12px;">{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    @if ($user->user_id === Auth::guard('user')->id())
                                        <button  hidden class="btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button  hidden class="btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn-edit"
                                            onclick="openEditUser({{ $user->user_id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->phone }}', '{{ $user->role }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-delete"
                                            onclick="deleteUser({{ $user->user_id }}, this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="7">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════ TAB: TRANSACTIONS ═══════════════ --}}
    <div id="tab-transactions" class="tab-panel">
        <div class="section">
            <div class="stats-row">
                <div class="stat-card">
                    <h2>{{ $txStats['total'] }}</h2>
                    <p>Total</p>
                </div>
                <div class="stat-card orange">
                    <h2>{{ $txStats['pending'] }}</h2>
                    <p>Pending</p>
                </div>
                <div class="stat-card blue">
                    <h2>{{ $txStats['accepted'] }}</h2>
                    <p>Accepted</p>
                </div>
                <div class="stat-card red">
                    <h2>{{ $txStats['rejected'] }}</h2>
                    <p>Rejected</p>
                </div>
            </div>

            <div class="toolbar">
                <input type="text" id="txSearch" placeholder="🔍 Search by buyer or seller…"
                    oninput="filterTx()">
                <select id="txStatus" onchange="filterTx()">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Buyer</th>
                            <th>Seller</th>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="txBody">
                        @forelse($transactions as $i => $t)
                            @php $txItem = $t->items->first(); @endphp
                            <tr data-buyer="{{ strtolower($t->buyer->name) }}"
                                data-seller="{{ strtolower($t->seller->name) }}" data-status="{{ $t->status }}">
                                <td style="color:#aaa;">{{ $i + 1 }}</td>
                                <td><span class="badge-pill badge-blue">{{ $t->buyer->name }}</span></td>
                                <td><span class="badge-pill badge-orange">{{ $t->seller->name }}</span></td>
                                <td>
                                    @if ($txItem)
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <img class="item-img"
                                                src="{{ asset('storage/uploaded/' . $txItem->item->image) }}"
                                                onerror="this.src='/assets/shared/images/default.png'" alt="">
                                            <span
                                                style="font-size:12px;color:#555;">{{ $txItem->item->material->category ?? '—' }}</span>
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><span
                                        class="price-tag">{{ $txItem ? number_format($txItem->price_at_purchase, 2) . ' EGP' : '—' }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge-pill
                                {{ $t->status === 'accepted' ? 'badge-green' : ($t->status === 'rejected' ? 'badge-red' : 'badge-orange') }}">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                </td>
                                <td style="color:#aaa;font-size:12px;">{{ $t->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="7">No transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════ TAB: SUSTAINABILITY ═══════════════ --}}
    <div id="tab-reports" class="tab-panel">
        <div class="section">
            <div class="stats-row">
                <div class="stat-card">
                    <h2>{{ $sustainability['reused_items'] }}</h2>
                    <p><i class="fas fa-recycle me-1"></i>Items Reused</p>
                </div>
                <div class="stat-card blue">
                    <h2>{{ $sustainability['completed_swaps'] }}</h2>
                    <p><i class="fas fa-sync me-1"></i>Completed Swaps</p>
                </div>
                <div class="stat-card orange">
                    <h2>{{ $sustainability['completed_requests'] }}</h2>
                    <p><i class="fas fa-check me-1"></i>Completed Requests</p>
                </div>
                <div class="stat-card purple">
                    <h2>{{ $sustainability['total_materials'] }}</h2>
                    <p><i class="fas fa-leaf me-1"></i>Materials Used</p>
                </div>
            </div>

            <div class="report-grid">

                <!-- Material Usage -->
                <div class="report-card">
                    <h4><i class="fas fa-tshirt me-1"></i>Material Usage</h4>
                    @foreach ($sustainability['material_stats'] as $mat)
                        <div class="bar-row">
                            <span class="bar-label">{{ $mat->material_name }}</span>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:{{ $mat->percentage }}%"></div>
                            </div>
                            <span class="bar-val">{{ $mat->count }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Transaction Status -->
                <div class="report-card">
                    <h4><i class="fas fa-chart-bar me-1"></i>Transaction Status</h4>
                    <div class="bar-row">
                        <span class="bar-label">Accepted</span>
                        <div class="bar-track">
                            <div class="bar-fill"
                                style="width:{{ $sustainability['tx_accepted_pct'] }}%;background:#198754;"></div>
                        </div>
                        <span class="bar-val">{{ $txStats['accepted'] }}</span>
                    </div>
                    <div class="bar-row">
                        <span class="bar-label">Pending</span>
                        <div class="bar-track">
                            <div class="bar-fill"
                                style="width:{{ $sustainability['tx_pending_pct'] }}%;background:#f0a500;"></div>
                        </div>
                        <span class="bar-val">{{ $txStats['pending'] }}</span>
                    </div>
                    <div class="bar-row">
                        <span class="bar-label">Rejected</span>
                        <div class="bar-track">
                            <div class="bar-fill"
                                style="width:{{ $sustainability['tx_rejected_pct'] }}%;background:#e74c3c;"></div>
                        </div>
                        <span class="bar-val">{{ $txStats['rejected'] }}</span>
                    </div>
                </div>

                <!-- Swap Status -->
                <div class="report-card">
                    <h4><i class="fas fa-sync me-1"></i>Swap Status</h4>
                    <div class="bar-row">
                        <span class="bar-label">Accepted</span>
                        <div class="bar-track">
                            <div class="bar-fill"
                                style="width:{{ $sustainability['swap_accepted_pct'] }}%;background:#198754;"></div>
                        </div>
                        <span class="bar-val">{{ $sustainability['swap_accepted'] }}</span>
                    </div>
                    <div class="bar-row">
                        <span class="bar-label">Pending</span>
                        <div class="bar-track">
                            <div class="bar-fill"
                                style="width:{{ $sustainability['swap_pending_pct'] }}%;background:#f0a500;"></div>
                        </div>
                        <span class="bar-val">{{ $sustainability['swap_pending'] }}</span>
                    </div>
                    <div class="bar-row">
                        <span class="bar-label">Rejected</span>
                        <div class="bar-track">
                            <div class="bar-fill"
                                style="width:{{ $sustainability['swap_rejected_pct'] }}%;background:#e74c3c;"></div>
                        </div>
                        <span class="bar-val">{{ $sustainability['swap_rejected'] }}</span>
                    </div>
                </div>

                <!-- Items by Category -->
                <div class="report-card">
                    <h4><i class="fas fa-tags me-1"></i>Items by Category</h4>
                    @foreach ($sustainability['category_stats'] as $cat)
                        <div class="bar-row">
                            <span class="bar-label">{{ ucfirst($cat->category) }}</span>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:{{ $cat->percentage }}%;background:#0d6efd;">
                                </div>
                            </div>
                            <span class="bar-val" style="color:#0d6efd;">{{ $cat->count }}</span>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <h3><i class="fas fa-edit me-2" style="color:#198754;"></i>Edit User</h3>
            <input type="hidden" id="editUserId" name="">
            <label>Name</label>
            <input type="text" id="editName" placeholder="Name" name="name" value="{{old('name')}}">
            @error('name')
                <p class="alert alert-danger fs-6 mt-3">{{ $message }}</p>                
            @enderror
            <label>Email</label>
            <input type="email" id="editEmail" placeholder="Email" name="email" value="{{old('email')}}">
            @error('email')
                <p class="alert alert-danger fs-6 mt-3">{{ $message }}</p>                
            @enderror
            <label>Phone</label>
            <input type="text" id="editPhone" placeholder="Phone" name="phone" value="{{old('phone')}}">
            @error('phone')
                <p class="alert alert-danger fs-6 mt-3">{{ $message }}</p>                
            @enderror
            <label>Role</label>
            <select id="editRole">
                <option value="buyer">Buyer</option>
                <option value="seller">Seller</option>
                <option value="admin">Admin</option>
            </select>
            <div class="modal-btns">
                <button class="btn-close-modal" onclick="closeEditModal()">Cancel</button>
                <button class="btn-save" onclick="saveUser()">Save Changes</button>
            </div>
        </div>
    </div>

    <div id="toast"></div>

    <script src="{{ asset('assets/shared/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/shared/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/admin/js/admin.js') }}"></script>
</body>

</html>
