<?php
// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function setUserCookie($userId, $username) {
    // Set secure cookies that expire in 30 days
    $expiry = time() + (86400 * 30);
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    
    setcookie('user_id', $userId, $expiry, '/', '', $secure, true);
    setcookie('username', $username, $expiry, '/', '', $secure, true);
}

function clearUserCookie() {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    
    setcookie('user_id', '', time() - 3600, '/', '', $secure, true);
    setcookie('username', '', time() - 3600, '/', '', $secure, true);
    
    // Clear all session data
    $_SESSION = array();
    session_destroy();
    session_start();
}

function isLoggedIn() {
    // Check session timeout
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        logout();
        return false;
    }
    
    // Update last activity time
    if (isset($_SESSION['user_id'])) {
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    return false;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /api/pages/login.php');
        exit();
    }
}

function requireRole($allowedRoles) {
    requireLogin();
    
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
        http_response_code(403);
        echo '<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>';
        exit();
    }
}

function hasRole($role) {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function hasAnyRole($roles) {
    return isLoggedIn() && isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}

function logout() {
    clearUserCookie();
}

function regenerateSession() {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
}

function loginUser($userId, $username, $role) {
    // Regenerate session ID to prevent session fixation
    regenerateSession();
    
    // Set session variables
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['last_activity'] = time();
    $_SESSION['login_time'] = time();
    
    // Set secure cookies
    setUserCookie($userId, $username);
}

// Rate limiting for login attempts
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 900) { // 15 minutes
    $key = 'login_attempts_' . $identifier;
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
    }
    
    $attempts = $_SESSION[$key];
    
    // Reset if time window has passed
    if (time() - $attempts['first_attempt'] > $timeWindow) {
        $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
        return true;
    }
    
    return $attempts['count'] < $maxAttempts;
}

function recordFailedLogin($identifier) {
    $key = 'login_attempts_' . $identifier;
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
    }
    
    $_SESSION[$key]['count']++;
}

function resetLoginAttempts($identifier) {
    $key = 'login_attempts_' . $identifier;
    unset($_SESSION[$key]);
}

// Validate and sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password) {
    // Password must be at least 8 characters long
    // Must contain at least one uppercase letter, one lowercase letter, and one number
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}
?>