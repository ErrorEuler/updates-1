<?php
// Only show for users with dual roles
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../config/Database.php';

$database = new Database();
$db = $database->connect();
$authService = new AuthService($db);

$userRoles = $authService->getUserRoles($_SESSION['user_id']);
$current_role_id = $_SESSION['role_id'];

// Check if user has dual role (both Dean and Chair)
if ($userRoles && $userRoles['secondary_role_id']) {
    $available_roles = [
        ['id' => $userRoles['role_id'], 'name' => $userRoles['primary_role']],
        ['id' => $userRoles['secondary_role_id'], 'name' => $userRoles['secondary_role']]
    ];
?>
    <div class="role-switcher bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <div>
                    <span class="text-sm font-medium text-blue-800">Current Role:</span>
                    <span class="ml-2 text-sm font-semibold text-blue-900 bg-blue-100 px-2 py-1 rounded">
                        <?= htmlspecialchars($_SESSION['role_name']) ?>
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <span class="text-sm text-blue-700 hidden sm:block">Switch to:</span>
                <select id="roleSwitch" class="text-sm border border-blue-300 rounded px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-auto">
                    <?php foreach ($available_roles as $role): ?>
                        <?php if ($role['id'] != $current_role_id): ?>
                            <option value="<?= $role['id'] ?>">
                                <?= htmlspecialchars($role['name']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <button onclick="switchRole()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded transition duration-150 whitespace-nowrap">
                    Switch Role
                </button>
            </div>
        </div>

        <div class="mt-2 text-xs text-blue-600">
            <svg class="h-4 w-4 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            You have access to both Dean and Program Chair roles. Switch between them as needed.
        </div>
    </div>

    <script>
        function switchRole() {
            const newRoleId = document.getElementById('roleSwitch').value;
            const button = event.target;
            const originalText = button.textContent;

            // Show loading state
            button.textContent = 'Switching...';
            button.disabled = true;

            fetch('/api/switch-role.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        new_role_id: newRoleId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message and reload
                        button.textContent = 'Success! Reloading...';
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Failed to switch role: ' + data.message);
                        button.textContent = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error switching role. Please try again.');
                    button.textContent = originalText;
                    button.disabled = false;
                });
        }
    </script>
<?php
}
?>