<?php
ob_start();
?>

<style>
    :root {
        --gold: #D4AF37;
        --white: #FFFFFF;
        --gray-dark: #4B5563;
        --gray-light: #E5E7EB;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .slide-in-left {
        animation: slideInLeft 0.5s ease-in;
    }

    @keyframes slideInLeft {
        from {
            transform: translateX(-20px);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .modal {
        transition: opacity 0.3s ease;
    }

    .modal.hidden {
        opacity: 0;
        pointer-events: none;
    }

    .modal-content {
        transition: transform 0.3s ease;
    }

    .input-focus {
        transition: all 0.2s ease;
    }

    .input-focus:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }

    .btn-gold {
        background-color: var(--gold);
        color: var(--white);
    }

    .btn-gold:hover {
        background-color: #b8972e;
    }

    .loading::after {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid var(--gold);
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-left: 8px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .tab-active {
        background-color: var(--gold);
        color: var(--white);
    }

    .tab-active:hover {
        background-color: #b8972e;
    }
</style>

<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 bg-clip-text text-transparent bg-gradient-to-r from-yellow-600 to-yellow-400 slide-in-left">
                        User Management
                    </h1>
                    <p class="mt-2 text-gray-600 slide-in-right">Manage system users, roles, and permissions</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:shadow-lg transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($users); ?></p>
                    </div>
                </div>
            </div>
            <div class="card bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:shadow-lg transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count(array_filter($users, fn($u) => $u['is_active'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="card bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:shadow-lg transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Roles</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($roles); ?></p>
                    </div>
                </div>
            </div>
            <div class="card bg-white rounded-xl shadow-md p-6 border border-gray-200 hover:shadow-lg transition-all duration-200">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Departments</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($departments); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 mb-6">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="searchUsers" placeholder="Search users..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <select id="roleFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">All Roles</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['role_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($role['role_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="collegeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">All Colleges</option>
                            <?php foreach ($colleges as $college): ?>
                                <option value="<?php echo htmlspecialchars($college['college_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($college['college_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="departmentFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept['department_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($dept['department_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-6 border-b border-gray-200">
                <button id="tab-all" class="tab px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200 <?php echo !isset($_GET['tab']) || $_GET['tab'] === 'all' ? 'tab-active' : 'text-gray-500 hover:text-gray-700'; ?>" onclick="switchTab('all')">All Users</button>
                <button id="tab-active" class="tab px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200 <?php echo isset($_GET['tab']) && $_GET['tab'] === 'active' ? 'tab-active' : 'text-gray-500 hover:text-gray-700'; ?>" onclick="switchTab('active')">Active Users</button>
                <button id="tab-inactive" class="tab px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200 <?php echo isset($_GET['tab']) && $_GET['tab'] === 'inactive' ? 'tab-active' : 'text-gray-500 hover:text-gray-700'; ?>" onclick="switchTab('inactive')">Inactive Users</button>
                <button id="tab-pending" class="tab px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200 <?php echo isset($_GET['tab']) && $_GET['tab'] === 'pending' ? 'tab-active' : 'text-gray-500 hover:text-gray-700'; ?>" onclick="switchTab('pending')">Pending Users <span id="pending-count" class="ml-1 bg-red-500 text-white text-xs rounded-full px-2 py-0.5"><?php echo count(array_filter($users, fn($u) => $u['is_active'] === 0)); ?></span></button>
            </nav>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900" id="table-title">Users Directory</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" id="usersTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider resize-x cursor-col-resize">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider resize-x cursor-col-resize">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider resize-x cursor-col-resize">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider resize-x cursor-col-resize">College</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider resize-x cursor-col-resize">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider resize-x cursor-col-resize">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider resize-x cursor-col-resize">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                        <?php foreach ($users as $user): ?>
                            <tr class="user-row <?php echo !$user['is_active'] ? 'pending-user' : ($user['is_active'] ? 'active-user' : 'inactive-user'); ?> hover:bg-gray-50 transition-colors duration-150 cursor-pointer" data-user-id="<?php echo $user['user_id']; ?>" style="display: <?php echo !isset($_GET['tab']) || $_GET['tab'] === 'all' ? '' : 'none'; ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <?php if (!empty($user['profile_picture'])): ?>
                                                <img src="<?php echo htmlspecialchars($user['profile_picture'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile picture" class="h-10 w-10 rounded-full">
                                            <?php else: ?>
                                                <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-yellow-600">
                                                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($user['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <?php echo htmlspecialchars($user['role_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($user['college_name'] ?? 'Not assigned', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($user['department_name'] ?? 'Not assigned', ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php $isActive = isset($user['is_active']) && $user['is_active']; ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $isActive ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="openAcceptModal(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8'); ?>')" class="text-green-600 hover:text-green-900 p-1 rounded transition-colors <?php echo !$user['is_active'] ? '' : 'hidden'; ?>">
                                            <span class="text-xl">✔</span>
                                        </button>
                                        <button onclick="openRejectModal(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8'); ?>')" class="text-red-600 hover:text-red-900 p-1 rounded transition-colors <?php echo !$user['is_active'] ? '' : 'hidden'; ?>">
                                            <span class="text-xl">✖</span>
                                        </button>
                                        <button onclick="editUser(<?php echo $user['user_id']; ?>)" class="text-green-600 hover:text-green-900 p-1 rounded transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <?php if ($user['is_active']): ?>
                                            <button onclick="disableUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8'); ?>')" class="text-red-600 hover:text-red-900 p-1 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Accept User Confirmation Modal -->
    <div id="acceptUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="relative mx-auto p-6 border-2 border-green-500 w-96 shadow-lg rounded-xl bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Accept User Account</h3>
                <div id="acceptUserDetails" class="mt-2 px-7 py-3 text-left">
                    <!-- Populated by JS -->
                </div>
                <div class="flex justify-center space-x-3 px-4 py-3">
                    <button onclick="closeAcceptUserModal()" class="px-4 py-2 bg-white text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmAcceptUser()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                        Accept User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Decline User Confirmation Modal -->
    <div id="declineUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="relative mx-auto p-6 border-2 border-red-500 w-96 shadow-lg rounded-xl bg-white">
            <input type="hidden" id="rejectUserId" class="user-id">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Reject User Account</h3>
                <div id="rejectUserDetails" class="mt-2 px-7 py-3 text-left">
                    <!-- Populated by JS -->
                </div>
                <div class="flex justify-center space-x-3 px-4 py-3">
                    <button onclick="closeDeclineUserModal()" class="px-4 py-2 bg-white text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmRejectUser()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                        Reject User
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Disable User Confirmation Modal -->
<div id="disableUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="relative mx-auto p-6 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Disable User Account</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to disable <span id="disableUserName" class="font-semibold"></span>? This action will prevent the user from logging in.
                </p>
            </div>
            <div class="flex justify-center space-x-3 px-4 py-3">
                <button onclick="closeDisableUserModal()" class="px-4 py-2 bg-white text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmDisableUser()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                    Disable User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div id="viewUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="relative mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900">User Details</h3>
            <button onclick="closeViewUserModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="userDetailsContent" class="space-y-4">
            <!-- Populated by JavaScript -->
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Fix the undefined _currentUserId error
    let _currentUserId = null;

    // Global protection for currentUserId
    Object.defineProperty(window, 'currentUserId', {
        get: function() {
            return _currentUserId;
        },
        set: function(value) {
            console.log('currentUserId set to:', value);
            _currentUserId = value;
        }
    });

    // Prevent accidental modification
    Object.freeze(window.currentUserId);


    // Add this to debug any accidental resets
    setInterval(() => {
        if (window._currentUserId === null || window._currentUserId === undefined) {
            console.log('WARNING: currentUserId is null/undefined. Stack trace:', new Error().stack);
        }
    }, 1000);

    function disableUser(userId, userName) {
        currentUserId = userId;
        document.getElementById('disableUserName').textContent = userName;
        document.getElementById('disableUserModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function declineUser(userId, userName) {
        currentUserId = userId;
        document.getElementById('declineUserName').textContent = userName;
        document.getElementById('declineUserModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeclineUserModal() {
        console.log('closeDeclineUserModal: currentUserId before close:', currentUserId);
        document.getElementById('declineUserModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        // DON'T reset currentUserId here - let confirmRejectUser handle it
        console.log('closeDeclineUserModal: currentUserId after close:', currentUserId);
    }

    function openAcceptModal(userId, userName) {
        currentUserId = userId; // Set it here explicitly
        const userRow = document.querySelector(`.user-row[data-user-id="${userId}"]`);
        const cells = userRow.getElementsByTagName('td');
        const userDetails = `
            <p class="text-sm text-gray-500">Username: @${cells[0].querySelector('.text-gray-500').textContent}</p>
            <p class="text-sm text-gray-500">Email: ${cells[1].textContent}</p>
            <p class="text-sm text-gray-500">Full Name: ${cells[0].querySelector('.text-gray-900').textContent}</p>
            <p class="text-sm text-gray-500">Role: ${cells[2].textContent}</p>
            <p class="text-sm text-gray-500">College: ${cells[3].textContent}</p>
            <p class="text-sm text-gray-500">Department: ${cells[4].textContent}</p>
        `;
        document.getElementById('acceptUserDetails').innerHTML = userDetails;
        document.getElementById('acceptUserModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAcceptUserModal() {
        console.log('closeAcceptUserModal: currentUserId before close:', currentUserId);
        document.getElementById('acceptUserModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        // DON'T reset currentUserId here - let confirmAcceptUser handle it
        console.log('closeAcceptUserModal: currentUserId after close:', currentUserId);
    }


    function openAcceptModal(userId, userName) {
        console.log('openAcceptModal called with userId:', userId, 'userName:', userName);

        if (!userId) {
            console.error('openAcceptModal: No userId provided');
            alert('Error: No user ID provided');
            return;
        }

        // Set both variables for redundancy
        currentUserId = parseInt(userId);
        window._currentUserId = parseInt(userId);

        console.log('currentUserId set to:', currentUserId, 'window._currentUserId:', window._currentUserId);

        const userRow = document.querySelector(`.user-row[data-user-id="${userId}"]`);
        if (!userRow) {
            console.error('openAcceptModal: User row not found for ID:', userId);
            alert('Error: User data not found');
            return;
        }

        const cells = userRow.getElementsByTagName('td');
        const userDetails = `
        <p class="text-sm text-gray-500">Username: @${cells[0].querySelector('.text-gray-500').textContent.trim()}</p>
        <p class="text-sm text-gray-500">Email: ${cells[1].textContent.trim()}</p>
        <p class="text-sm text-gray-500">Full Name: ${cells[0].querySelector('.text-gray-900').textContent.trim()}</p>
        <p class="text-sm text-gray-500">Role: ${cells[2].textContent.trim()}</p>
        <p class="text-sm text-gray-500">College: ${cells[3].textContent.trim()}</p>
        <p class="text-sm text-gray-500">Department: ${cells[4].textContent.trim()}</p>
    `;
        document.getElementById('acceptUserDetails').innerHTML = userDetails;
        document.getElementById('acceptUserModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        console.log('Modal opened successfully for user:', currentUserId);
    }

    function openRejectModal(userId, userName) {
        console.log('openRejectModal called with userId:', userId, 'userName:', userName);

        if (!userId) {
            console.error('openRejectModal: No userId provided');
            alert('Error: No user ID provided');
            return;
        }

        // Set multiple backups
        currentUserId = parseInt(userId);
        window._currentUserId = parseInt(userId);
        document.getElementById('rejectUserId').value = userId;

        console.log('Multiple user ID backups set:', {
            currentUserId: currentUserId,
            _currentUserId: window._currentUserId,
            hiddenField: document.getElementById('rejectUserId').value
        });

        const userRow = document.querySelector(`.user-row[data-user-id="${userId}"]`);
        if (!userRow) {
            console.error('openRejectModal: User row not found for ID:', userId);
            alert('Error: User data not found');
            return;
        }

        const cells = userRow.getElementsByTagName('td');
        const userDetails = `
        <p class="text-sm text-gray-500">Username: @${cells[0].querySelector('.text-gray-500').textContent.trim()}</p>
        <p class="text-sm text-gray-500">Email: ${cells[1].textContent.trim()}</p>
        <p class="text-sm text-gray-500">Full Name: ${cells[0].querySelector('.text-gray-900').textContent.trim()}</p>
        <p class="text-sm text-gray-500">Role: ${cells[2].textContent.trim()}</p>
        <p class="text-sm text-gray-500">College: ${cells[3].textContent.trim()}</p>
        <p class="text-sm text-gray-500">Department: ${cells[4].textContent.trim()}</p>
        <textarea id="rejectReason" class="mt-2 w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" placeholder="Enter rejection reason" rows="3" required></textarea>
    `;
        document.getElementById('rejectUserDetails').innerHTML = userDetails;
        document.getElementById('declineUserModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        console.log('Reject modal opened successfully for user:', currentUserId);
    }

    function debugPendingUsers() {
        console.log('=== DEBUG PENDING USERS ===');
        const acceptButtons = document.querySelectorAll('button[onclick*="openAcceptModal"]');
        const rejectButtons = document.querySelectorAll('button[onclick*="openRejectModal"]');

        console.log('Accept buttons found:', acceptButtons.length);
        console.log('Reject buttons found:', rejectButtons.length);

        acceptButtons.forEach((button, index) => {
            const onclick = button.getAttribute('onclick');
            const match = onclick.match(/openAcceptModal\((\d+),/);
            if (match) {
                console.log(`Accept button ${index + 1}: user_id=${match[1]}`);
            }
        });

        // Check if any users are actually pending (should have accept/reject buttons visible)
        const pendingRows = document.querySelectorAll('.pending-user');
        console.log('Pending user rows found:', pendingRows.length);

        pendingRows.forEach(row => {
            const userId = row.getAttribute('data-user-id');
            const statusCell = row.querySelector('td:nth-child(6)');
            console.log(`Pending user ID: ${userId}, Status: ${statusCell.textContent.trim()}`);
        });

        console.log('=== END PENDING DEBUG ===');
    }


    function confirmAcceptUser() {
        console.log('confirmAcceptUser called - currentUserId:', currentUserId, 'window._currentUserId:', window._currentUserId);

        // Use the protected version
        const userIdToUse = window._currentUserId || currentUserId;

        if (!userIdToUse) {
            console.error('confirmAcceptUser: No user ID set. Available variables:', {
                currentUserId: currentUserId,
                _currentUserId: window._currentUserId,
                userIdToUse: userIdToUse
            });
            alert('Error: No user selected. Please try again.');
            return;
        }

        console.log('Accepting user_id:', userIdToUse);
        console.log('CSRF Token:', '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>');

        fetch(`/admin/users`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>'
                },
                body: JSON.stringify({
                    action: 'approve',
                    user_id: userIdToUse
                })
            })
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Only reset currentUserId after successful operation
                    currentUserId = null;
                    window._currentUserId = null;
                    closeAcceptUserModal();
                    location.reload();
                } else {
                    alert('Failed to accept user: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while accepting the user: ' + error.message);
            });
    }

    function confirmRejectUser() {
        console.log('confirmRejectUser called - currentUserId:', currentUserId, 'window._currentUserId:', window._currentUserId);

        const userIdToUse = currentUserId || window._currentUserId;

        if (!userIdToUse) {
            console.error('confirmRejectUser: No user ID available');
            alert('Error: No user selected. Please try reopening the reject modal.');
            return;
        }

        const reason = document.getElementById('rejectReason')?.value || 'No reason provided';
        console.log('Rejecting user_id:', userIdToUse, 'Reason:', reason);

        // Show loading state
        const rejectBtn = document.querySelector('#declineUserModal button[onclick="confirmRejectUser()"]');
        const originalText = rejectBtn.textContent;
        rejectBtn.textContent = 'Rejecting...';
        rejectBtn.disabled = true;

        fetch(`/admin/users`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>'
                },
                body: JSON.stringify({
                    action: 'reject',
                    user_id: parseInt(userIdToUse),
                    reason: reason
                })
            })
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                return response.text().then(text => {
                    console.log('Raw response:', text);

                    // Extract JSON from response even if there are PHP warnings
                    let jsonData = null;

                    // Method 1: Try to find JSON in the response
                    const jsonMatch = text.match(/\{.*\}/s);
                    if (jsonMatch) {
                        try {
                            jsonData = JSON.parse(jsonMatch[0]);
                            console.log('Extracted JSON from response:', jsonData);
                        } catch (e) {
                            console.error('Failed to parse extracted JSON:', e);
                        }
                    }

                    // Method 2: If no JSON found but response looks successful, assume success
                    if (!jsonData && response.status === 200) {
                        console.log('No JSON found but status is 200, assuming success');
                        jsonData = {
                            success: true,
                            message: 'Operation completed'
                        };
                    }

                    // Method 3: If still no data, throw error
                    if (!jsonData) {
                        throw new Error('Could not parse server response');
                    }

                    return jsonData;
                });
            })
            .then(data => {
                console.log('Processed response data:', data);

                if (data.success) {
                    // Show success message
                    showToast('User rejected successfully', 'success');

                    // Close modal first
                    closeDeclineUserModal();

                    // Remove the rejected user from the UI without reloading
                    removeUserFromUI(userIdToUse);

                    // Update pending count
                    updatePendingCount();

                    // Reset user IDs
                    currentUserId = null;
                    window._currentUserId = null;
                } else {
                    alert('Failed to reject user: ' + (data.error || data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Even if there's an error parsing, check if the operation might have succeeded
                if (error.message.includes('Could not parse') || error.message.includes('PHP Warning')) {
                    // Show a warning but assume it might have worked
                    showToast('User rejection may have completed. Please check the user list.', 'warning');
                    closeDeclineUserModal();
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    alert('An error occurred while rejecting the user: ' + error.message);
                }
            })
            .finally(() => {
                // Reset button state
                rejectBtn.textContent = originalText;
                rejectBtn.disabled = false;
            });
    }

    // Add these helper functions
    function removeUserFromUI(userId) {
        const userRow = document.querySelector(`.user-row[data-user-id="${userId}"]`);
        if (userRow) {
            userRow.remove();
            console.log('Removed user row from UI:', userId);
        } else {
            console.log('User row not found for removal:', userId);
        }
    }

    function updatePendingCount() {
        const pendingRows = document.querySelectorAll('.pending-user');
        const pendingCount = pendingRows.length;
        const pendingCountElement = document.getElementById('pending-count');

        if (pendingCountElement) {
            pendingCountElement.textContent = pendingCount;
            if (pendingCount === 0) {
                pendingCountElement.style.display = 'none';
            } else {
                pendingCountElement.style.display = 'inline';
            }
        }

        console.log('Updated pending count:', pendingCount);
    }

    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        type === 'warning' ? 'bg-yellow-500 text-black' : 
        'bg-blue-500 text-white'
    }`;
        toast.textContent = message;

        document.body.appendChild(toast);

        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }


    function confirmDeclineUser() {
        if (currentUserId) {
            fetch(`/admin/users?action=decline&user_id=${currentUserId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDeclineUserModal();
                        location.reload();
                    } else {
                        alert('Failed to decline user: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while declining the user');
                });
        }
    }

    // Update approveUser to include confirmation
    function approveUser(userId, userName) {
        if (confirm(`Are you sure you want to approve ${userName}?`)) {
            fetch(`/admin/users?action=approve&user_id=${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to approve user: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the user');
                });
        }
    }

    function enableUser(userId, userName) {
        if (confirm(`Are you sure you want to enable ${userName}?`)) {
            fetch(`/admin/users?action=enable&user_id=${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to enable user: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while enabling the user');
                });
        }
    }


    function closeDisableUserModal() {
        document.getElementById('disableUserModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentUserId = null;
    }

    function confirmDisableUser() {
        if (currentUserId) {
            fetch(`/admin/users?action=disable&user_id=${currentUserId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDisableUserModal();
                        location.reload();
                    } else {
                        alert('Failed to disable user: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while disabling the user');
                });
        }
    }

    function showUserDetails(row) {
        currentUserId = row.getAttribute('data-user-id');
        const cells = row.getElementsByTagName('td');
        const user = {
            username: cells[0].querySelector('.text-gray-500').textContent.replace('@', ''),
            email: cells[1].textContent,
            first_name: cells[0].querySelector('.text-gray-900').textContent.split(' ')[0],
            last_name: cells[0].querySelector('.text-gray-900').textContent.split(' ')[1],
            role_name: cells[2].textContent,
            college_name: cells[3].textContent,
            department_name: cells[4].textContent,
            is_active: cells[5].textContent === 'Inactive'
        };
        const content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700">Username</label><p class="mt-1 text-gray-900">${user.username}</p></div>
                    <div><label class="block text-sm font-medium text-gray-700">Email</label><p class="mt-1 text-gray-900">${user.email}</p></div>
                    <div><label class="block text-sm font-medium text-gray-700">Full Name</label><p class="mt-1 text-gray-900">${user.first_name} ${user.last_name}</p></div>
                    <div><label class="block text-sm font-medium text-gray-700">Role</label><p class="mt-1 text-gray-900">${user.role_name}</p></div>
                    <div><label class="block text-sm font-medium text-gray-700">College</label><p class="mt-1 text-gray-900">${user.college_name}</p></div>
                    <div><label class="block text-sm font-medium text-gray-700">Department</label><p class="mt-1 text-gray-900">${user.department_name}</p></div>
                    <div><label class="block text-sm font-medium text-gray-700">Status</label><p class="mt-1 text-gray-900">${user.is_active ? 'Inactive' : 'Active'}</p></div>
                </div>
            `;
        document.getElementById('userDetailsContent').innerHTML = content;
        document.getElementById('viewUserModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeViewUserModal() {
        document.getElementById('viewUserModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentUserId = null;
    }

    // ... (other functions like approveUser, filterTable, etc.)

    // Event listeners
    document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('.user-row');
        rows.forEach(row => {
            row.addEventListener('click', () => showUserDetails(row));
        });

        const defaultTab = '<?php echo isset($_GET['tab']) ? htmlspecialchars($_GET['tab'], ENT_QUOTES, 'UTF-8') : 'all'; ?>';
        if (['all', 'active', 'inactive', 'pending'].includes(defaultTab)) {
            switchTab(defaultTab);
        } else {
            switchTab('all');
        }
    });

    function approveUser(userId, userName) {
        if (confirm(`Are you sure you want to approve ${userName}?`)) {
            fetch(`/admin/users?action=approve&user_id=${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to approve user: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the user');
                });
        }
    }

    // Search and filter functionality
    document.getElementById('searchUsers').addEventListener('input', function() {
        filterTable();
    });

    document.getElementById('roleFilter').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('collegeFilter').addEventListener('change', function() {
        filterTable();
    });

    function switchTab(tab) {
        console.log('Switching to tab:', tab);

        const tabs = document.querySelectorAll('.tab');
        const rows = document.querySelectorAll('.user-row');

        tabs.forEach(t => t.classList.remove('tab-active'));
        document.getElementById(`tab-${tab}`).classList.add('tab-active');
        document.getElementById('table-title').textContent = `${tab.charAt(0).toUpperCase() + tab.slice(1)} Users`;

        rows.forEach(row => {
            const isPending = row.classList.contains('pending-user');
            const isActive = row.classList.contains('active-user');
            const isInactive = row.classList.contains('inactive-user');

            console.log(`User ${row.getAttribute('data-user-id')}: pending=${isPending}, active=${isActive}, inactive=${isInactive}`);

            row.style.display = 'none';
            if (tab === 'all') row.style.display = '';
            else if (tab === 'active' && isActive) row.style.display = '';
            else if (tab === 'inactive' && isInactive) row.style.display = '';
            else if (tab === 'pending' && isPending) row.style.display = '';
        });

        window.history.pushState({}, '', `?tab=${tab}`);
        filterTable();

        // Debug after tab switch
        setTimeout(() => {
            debugPendingUsers();
        }, 100);
    }

    function filterTable() {
        const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
        const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
        const collegeFilter = document.getElementById('collegeFilter').value.toLowerCase();
        const activeTab = document.querySelector('.tab-active')?.id.replace('tab-', '') || 'all';

        const rows = document.querySelectorAll('.user-row');
        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            if (cells.length > 0) {
                const userName = cells[0].textContent.toLowerCase();
                const userEmail = cells[1].textContent.toLowerCase();
                const userRole = cells[2].textContent.toLowerCase();
                const userCollege = cells[3].textContent.toLowerCase();
                const isVisible = (activeTab === 'all' ||
                    (activeTab === 'active' && row.classList.contains('active-user')) ||
                    (activeTab === 'inactive' && row.classList.contains('inactive-user')) ||
                    (activeTab === 'pending' && row.classList.contains('pending-user')));

                const matchesSearch = userName.includes(searchTerm) || userEmail.includes(searchTerm);
                const matchesRole = roleFilter === '' || userRole.includes(roleFilter);
                const matchesCollege = collegeFilter === '' || userCollege.includes(collegeFilter);

                row.style.display = isVisible && matchesSearch && matchesRole && matchesCollege ? '' : 'none';
            }
        });
    }

    // Close modals when clicking outside
    /* window.addEventListener('click', function(event) {
         const addModal = document.getElementById('addUserModal');
         const disableModal = document.getElementById('disableUserModal');
         const viewModal = document.getElementById('viewUserModal');
         const editModal = document.getElementById('editUserModal');

         if (event.target === addModal) closeAddUserModal();
         if (event.target === disableModal) closeDisableUserModal();
         if (event.target === viewModal) closeViewUserModal();
         if (event.target === editModal) closeEditUserModal();
     });  

     // Close modals with Escape key
     document.addEventListener('keydown', function(event) {
         if (event.key === 'Escape') {
             closeAddUserModal();
             closeDisableUserModal();
             closeViewUserModal();
             closeEditUserModal();
         }
     }); */

    // Column resizing
    const thElements = document.querySelectorAll('th[role="columnheader"]');
    thElements.forEach(th => {
        let startX, startWidth;

        th.addEventListener('mousedown', (e) => {
            startX = e.pageX;
            startWidth = th.offsetWidth;
            th.style.userSelect = 'none';

            function resize(e) {
                const diff = e.pageX - startX;
                th.style.width = (startWidth + diff) + 'px';
            }

            function stopResize() {
                document.removeEventListener('mousemove', resize);
                document.removeEventListener('mouseup', stopResize);
                th.style.userSelect = '';
            }

            document.addEventListener('mousemove', resize);
            document.addEventListener('mouseup', stopResize);
        });
    });



    // Initialize default tab and row click events
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize default tab
        const defaultTab = '<?php echo isset($_GET['tab']) ? htmlspecialchars($_GET['tab'], ENT_QUOTES, 'UTF-8') : 'all'; ?>';
        if (['all', 'active', 'inactive', 'pending'].includes(defaultTab)) {
            switchTab(defaultTab);
        } else {
            switchTab('all');
        }

        // Update row click to avoid triggering on action buttons
        const rows = document.querySelectorAll('.user-row');
        rows.forEach(row => {
            row.addEventListener('click', (e) => {
                // Check if click is not on a button
                if (!e.target.closest('button')) {
                    showUserDetails(row);
                }
            });
        });
    });

    function debugUserData() {
        console.log('=== DEBUG USER DATA ===');
        const rows = document.querySelectorAll('.user-row');
        rows.forEach(row => {
            const userId = row.getAttribute('data-user-id');
            const cells = row.getElementsByTagName('td');
            console.log(`User ID: ${userId}, Name: ${cells[0].textContent}, Status: ${cells[5].textContent}`);
        });
        console.log('=== END DEBUG ===');
    }

    function checkButtonVisibility() {
        const acceptButtons = document.querySelectorAll('button[onclick*="openAcceptModal"]');
        const rejectButtons = document.querySelectorAll('button[onclick*="openRejectModal"]');

        acceptButtons.forEach(button => {
            const isVisible = button.offsetParent !== null;
            console.log(`Accept button for user ${button.getAttribute('onclick')} - visible: ${isVisible}`);
            if (!isVisible) {
                button.classList.add('debug-visible');
            }
        });

        rejectButtons.forEach(button => {
            const isVisible = button.offsetParent !== null;
            console.log(`Reject button for user ${button.getAttribute('onclick')} - visible: ${isVisible}`);
            if (!isVisible) {
                button.classList.add('debug-visible');
            }
        });
    }

    // Call this in your DOMContentLoaded event to verify data
    document.addEventListener('DOMContentLoaded', () => {
        debugUserData();
        debugPendingUsers(); // Add this line
        checkButtonVisibility(); // Add this line

        const rows = document.querySelectorAll('.user-row');
        rows.forEach(row => {
            row.addEventListener('click', (e) => {
                if (!e.target.closest('button')) {
                    showUserDetails(row);
                }
            });
        });

        const defaultTab = '<?php echo isset($_GET['tab']) ? htmlspecialchars($_GET['tab'], ENT_QUOTES, 'UTF-8') : 'all'; ?>';
        if (['all', 'active', 'inactive', 'pending'].includes(defaultTab)) {
            switchTab(defaultTab);
        } else {
            switchTab('all');
        }
    });
</script>
</div>

<?php

$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>