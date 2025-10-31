<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../services/AuthService.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    $new_role_id = $input['new_role_id'] ?? null;

    if (!$new_role_id) {
        throw new Exception('No role ID provided');
    }

    // Initialize services
    $database = new Database();
    $db = $database->connect();
    $authService = new AuthService($db);

    // Switch role
    $success = $authService->switchUserRole($_SESSION['user_id'], $new_role_id);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Role switched successfully',
            'new_role_id' => $new_role_id,
            'new_role_name' => $_SESSION['role_name'] ?? 'Unknown'
        ]);
    } else {
        throw new Exception('Failed to switch role. You may not have access to this role.');
    }
} catch (Exception $e) {
    error_log("Role switch error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
