<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../services/EmailService.php';

class AuthService
{
    private $userModel;
    private $db;
    private $emailService;

    public function __construct($db = null)
    {

        // If no database connection is provided, create one
        if ($db === null) {
            $database = new Database();
            $this->db = $database->connect();
        } else {
            $this->db = $db;
        }

        $this->db = $db;
        $this->userModel = new UserModel();
        $this->emailService = new EmailService();
    }

    /**
     * Login a user
     * @param string $employeeId
     * @param string $password
     * @return array|bool User data on success, false on failure
     */
    public function login($employeeId, $password)
    {
        try {
            $query = "
                SELECT u.user_id, u.employee_id, u.title, u.username, u.first_name, u.last_name, 
                       u.password_hash, u.role_id, u.profile_picture, u.is_active,
                       u.department_id, u.college_id, r.role_name
                FROM users u
                JOIN roles r ON u.role_id = r.role_id
                WHERE u.employee_id = :employee_id
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $user['is_active'] && password_verify($password, $user['password_hash'])) {
                $this->logAuthAction($user['user_id'], 'login_success', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                return [
                    'user_id' => $user['user_id'],
                    'employee_id' => $user['employee_id'],
                    'title' => $user['title'],
                    'username' => $user['username'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'role_id' => $user['role_id'],
                    'role_name' => $user['role_name'],
                    'department_id' => $user['department_id'],
                    'college_id' => $user['college_id'],
                    'profile_picture' => $user['profile_picture'] ?? null,
                ];
            } else {
                $this->logAuthAction(null, 'login_failed', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $employeeId);
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error during login for employee_id $employeeId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register a new user
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function register($data)
    {
        try {
            // Validate required fields
            $required_fields = ['employee_id', 'username', 'password', 'email', 'first_name', 'last_name', 'role_id', 'department_id', 'college_id'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            // Validate role_id
            $roles = $this->userModel->getRoles();
            $valid_role_ids = array_column($roles, 'role_id');
            if (!in_array($data['role_id'], $valid_role_ids)) {
                throw new Exception("Invalid role_id: {$data['role_id']}");
            }

            // Check for duplicates
            if ($this->userModel->employeeIdExists($data['employee_id'])) {
                throw new Exception("Employee ID {$data['employee_id']} already exists");
            }
            if ($this->userModel->emailExists($data['email'])) {
                throw new Exception("Email {$data['email']} already exists");
            }

            $this->db->beginTransaction();

            // Insert into users table (all users pending approval)
            $query = "
            INSERT INTO users (
                employee_id, username, password_hash, email, first_name, middle_name,
                last_name, suffix, role_id, department_id, college_id, is_active, created_at
            ) VALUES (
                :employee_id, :username, :password_hash, :email, :first_name, :middle_name,
                :last_name, :suffix, :role_id, :department_id, :college_id, 0, NOW()
            )";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':employee_id' => $data['employee_id'],
                ':username' => $data['username'],
                ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':email' => $data['email'],
                ':first_name' => $data['first_name'],
                ':middle_name' => $data['middle_name'] ?? null,
                ':last_name' => $data['last_name'],
                ':suffix' => $data['suffix'] ?? null,
                ':role_id' => $data['role_id'],
                ':department_id' => $data['department_id'],
                ':college_id' => $data['college_id']
            ]);
            $userId = $this->db->lastInsertId();
            error_log("register: Inserted user_id=$userId, employee_id={$data['employee_id']}, role_id={$data['role_id']}, is_active=0");

            // Handle role-specific table inserts FIRST (within transaction)
            if ($data['role_id'] == 4) { // Dean
                $startDate = $data['start_date'] ?? date('Y-m-d');
                $query = "
                INSERT INTO deans (user_id, college_id, start_date, is_current)
                VALUES (:user_id, :college_id, :start_date, 1)
            ";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':college_id' => $data['college_id'],
                    ':start_date' => $startDate
                ]);
                error_log("register: Inserted dean for user_id=$userId, college_id={$data['college_id']}");
            }

            if ($data['role_id'] == 5 && !empty($data['program_id'])) { // Program Chair
                $query = "
                INSERT INTO program_chairs (
                    user_id, program_id, is_current
                ) VALUES (
                    :user_id, :program_id, 1
                )";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':program_id' => $data['program_id']
                ]);
                error_log("register: Inserted program_chair for user_id=$userId, program_id={$data['program_id']}");
            }

            // For Faculty (role_id = 6), insert into faculty and faculty_departments
            if ($data['role_id'] == 6) {
                $query = "
                INSERT INTO faculty (
                    user_id, employee_id, academic_rank, employment_type, max_hours
                ) VALUES (
                    :user_id, :employee_id, :academic_rank, :employment_type, :max_hours
                )";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':employee_id' => $data['employee_id'],
                    ':academic_rank' => $data['academic_rank'] ?? 'Instructor',
                    ':employment_type' => $data['employment_type'] ?? 'Part-time',
                    ':max_hours' => $data['max_hours'] ?? 18.00
                ]);
                $facultyId = $this->db->lastInsertId();
                error_log("register: Inserted faculty_id=$facultyId for user_id=$userId");

                $query = "
                INSERT INTO faculty_departments (
                    faculty_id, department_id, is_primary
                ) VALUES (
                    :faculty_id, :department_id, 1
                )";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':faculty_id' => $facultyId,
                    ':department_id' => $data['department_id']
                ]);
                error_log("register: Inserted faculty_department for faculty_id=$facultyId, department_id={$data['department_id']}, is_primary=1");
            }

            // COMMIT the main transaction FIRST
            $this->db->commit();
            error_log("register: Main transaction committed for user_id=$userId");

            // 🆕 FIX: Handle dual role setup AFTER committing the main transaction
            $secondary_role_id = null;
            if (isset($data['has_dual_role']) && $data['has_dual_role'] == 1 && !empty($data['secondary_role_id'])) {
                $secondary_role_id = $data['secondary_role_id'];
                error_log("register: Setting up dual role for user_id=$userId - primary: {$data['role_id']}, secondary: $secondary_role_id");

                // Setup role switching in a SEPARATE operation
                $dualRoleSuccess = $this->setupUserRoles($userId, $data['role_id'], $secondary_role_id);

                if (!$dualRoleSuccess) {
                    error_log("register: Dual role setup failed for user_id=$userId, but user was created successfully");
                    // Don't throw error here - user was created, just dual role failed
                } else {
                    error_log("register: Dual role setup completed successfully for user_id=$userId");
                }
            } else {
                error_log("register: Single role setup completed for user_id=$userId - role: {$data['role_id']}");
            }

            // Send confirmation email to the new user
            $fullName = "{$data['first_name']} {$data['last_name']}";
            $roleName = array_column(array_filter($roles, fn($r) => $r['role_id'] == $data['role_id']), 'role_name')[0] ?? 'Unknown Role';
            $this->emailService->sendConfirmationEmail($data['email'], $fullName, $roleName);

            $this->logAuthAction($userId, 'request_submitted', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $data['employee_id']);
            return true;
        } catch (Exception $e) {
            // Only rollback if we're still in a transaction
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error during registration for employee_id {$data['employee_id']}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify CSRF token
     * @param string $token
     * @return bool
     */
    public function verifyCsrfToken($token)
    {
        $expectedToken = $_SESSION['csrf_token'] ?? '';
        $isValid = !empty($token) && hash_equals($expectedToken, $token);
        error_log("verifyCsrfToken: token=$token, expected=$expectedToken, isValid=" . ($isValid ? 'true' : 'false'));
        return $isValid;
    }

    /**
     * Generate CSRF token
     * @return string
     */
    public function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Log authentication actions
     * @param int|null $userId
     * @param string $action
     * @param string $ipAddress
     * @param string $userAgent
     * @param string|null $identifier
     * @return void
     */
    private function logAuthAction($userId, $action, $ipAddress, $userAgent, $identifier = null)
    {
        try {
            $query = "
                INSERT INTO auth_logs (user_id, action, ip_address, user_agent, identifier, created_at)
                VALUES (:user_id, :action, :ip_address, :user_agent, :identifier, NOW())
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':action', $action, PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $ipAddress, PDO::PARAM_STR);
            $stmt->bindParam(':user_agent', $userAgent, PDO::PARAM_STR);
            $stmt->bindParam(':identifier', $identifier, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error logging auth action for user_id " . ($userId ?? 'null') . ": " . $e->getMessage());
        }
    }

    /**
     * Start a session for a user
     * @param array $user
     * @return void
     */
    public function startSession($user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['employee_id'] = $user['employee_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['title'] = $user['title'] ?? null;
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['middle_name'] = $user['middle_name'] ?? null;
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['suffix'] = $user['suffix'] ?? null;
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['department_id'] = $user['department_id'];
        $_SESSION['college_id'] = $user['college_id'];
        $_SESSION['profile_picture'] = $user['profile_picture'] ?? null;
        $_SESSION['logged_in'] = true;

        session_regenerate_id(true);
    }

    /**
     * Destroy the current session
     * @return void
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }

    /**
     * Check if a user is logged in
     * @return bool
     */
    public function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    /**
     * Setup user roles including dual role configuration
     * @param int $user_id
     * @param int $primary_role
     * @param int|null $secondary_role
     * @return bool
     */
    public function setupUserRoles($user_id, $primary_role, $secondary_role = null)
    {
        try {
            $userModel = new UserModel();

            error_log("setupUserRoles: Starting for user_id=$user_id, primary_role=$primary_role, secondary_role=" . ($secondary_role ?? 'null'));

            if ($secondary_role) {
                // Validate that only Dean/Chair combinations are allowed
                $valid_combinations = [
                    [4, 5], // Dean + Chair
                    [5, 4]  // Chair + Dean
                ];

                $is_valid = false;
                foreach ($valid_combinations as $combo) {
                    if (($primary_role == $combo[0] && $secondary_role == $combo[1]) ||
                        ($primary_role == $combo[1] && $secondary_role == $combo[0])
                    ) {
                        $is_valid = true;
                        break;
                    }
                }

                if (!$is_valid) {
                    error_log("setupUserRoles: Invalid role combination for user_id=$user_id");
                    throw new Exception("Invalid role combination. Only Dean/Program Chair combinations are allowed.");
                }

                // 🆕 FIX: Use manual insert to avoid transaction issues
                error_log("setupUserRoles: Setting up dual role for user_id=$user_id");
                $success = $userModel->manualInsertRoleSwitching($user_id, $primary_role, $secondary_role);
                if (!$success) {
                    error_log("setupUserRoles: Failed to setup role switching for user_id=$user_id");
                    throw new Exception("Failed to setup role switching.");
                }
                error_log("setupUserRoles: Dual role setup completed for user_id=$user_id");
            } else {
                error_log("setupUserRoles: Single role setup for user_id=$user_id");
            }

            // Set initial active role
            $result = $userModel->switchActiveRole($user_id, $primary_role);
            error_log("setupUserRoles: Initial role set to $primary_role for user_id=$user_id, result=" . ($result ? 'success' : 'failed'));

            return $result;
        } catch (Exception $e) {
            error_log("Error setting up user roles: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Switch user's active role
     * @param int $user_id
     * @param int $new_role_id
     * @return bool
     */
    public function switchUserRole($user_id, $new_role_id)
    {
        try {
            $userModel = new UserModel();

            // Verify user has access to this role
            $available_roles = $userModel->getUserAvailableRoles($user_id);
            $has_access = false;

            foreach ($available_roles as $role) {
                if ($role['role_id'] == $new_role_id) {
                    $has_access = true;
                    break;
                }
            }

            if ($has_access) {
                $success = $userModel->switchActiveRole($user_id, $new_role_id);
                if ($success) {
                    // Update session
                    $this->updateSessionAfterRoleSwitch($user_id, $new_role_id);
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            error_log("Error switching user role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update session after role switch
     * @param int $user_id
     * @param int $new_role_id
     * @return void
     */
    private function updateSessionAfterRoleSwitch($user_id, $new_role_id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userModel = new UserModel();
        $user = $userModel->getUserById($user_id);

        if ($user) {
            $_SESSION['role_id'] = $new_role_id;
            $_SESSION['role_name'] = $this->getRoleName($new_role_id);

            // Log the role switch
            $this->logAuthAction($user_id, 'role_switch', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], "Switched to role: {$_SESSION['role_name']}");
        }
    }

    /**
     * Get role name by ID
     * @param int $role_id
     * @return string
     */
    public function getRoleName($role_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT role_name FROM roles WHERE role_id = :role_id");
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['role_name'] ?? 'Unknown';
        } catch (PDOException $e) {
            error_log("Error getting role name: " . $e->getMessage());
            return 'Unknown';
        }
    }

    /**
     * Get user roles information
     * @param int $user_id
     * @return array
     */
    public function getUserRoles($user_id)
    {
        $userModel = new UserModel();
        return $userModel->getUserWithMultipleRoles($user_id);
    }
}
