<?php
header('Content-Type: application/json; charset=utf-8');

// Simple JSON-based signup endpoint intended for AJAX requests from singin.html
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'message' => 'Invalid request method.']);
    exit;
}

$fullName = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// basic validation
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}
if (strlen($password) < 8) {
    echo json_encode(['ok' => false, 'message' => 'Password must be at least 8 characters.']);
    exit;
}
if ($username === '') {
    echo json_encode(['ok' => false, 'message' => 'Please choose a username.']);
    exit;
}

// connect to DB
$con = mysqli_connect('localhost', 'root', '', 'ldc');
if (!$con) {
    echo json_encode(['ok' => false, 'message' => 'Server error: cannot connect to database.']);
    exit;
}

// Check existing user by email or username
$checkSql = "SELECT `id` FROM `users` WHERE `Email` = ? OR `UserName` = ? LIMIT 1";
if ($stmt = mysqli_prepare($con, $checkSql)) {
    mysqli_stmt_bind_param($stmt, 'ss', $email, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        echo json_encode(['ok' => false, 'message' => 'An account with this email or username already exists.']);
        mysqli_close($con);
        exit;
    }
} else {
    echo json_encode(['ok' => false, 'message' => 'Server error: please try again later.']);
    mysqli_close($con);
    exit;
}

// Insert user
$hashed = password_hash($password, PASSWORD_DEFAULT);
$insertSql = "INSERT INTO `users` (`FullName`, `Email`, `UserName`, `Password`) VALUES (?, ?, ?, ?)";
if ($istmt = mysqli_prepare($con, $insertSql)) {
    mysqli_stmt_bind_param($istmt, 'ssss', $fullName, $email, $username, $hashed);
    if (mysqli_stmt_execute($istmt)) {
        // Optionally start session here
        session_start();
        $_SESSION['userName'] = $email;

        echo json_encode(['ok' => true]);
        mysqli_close($con);
        exit;
    } else {
        echo json_encode(['ok' => false, 'message' => 'Server error: unable to create account.']);
        mysqli_close($con);
        exit;
    }
} else {
    echo json_encode(['ok' => false, 'message' => 'Server error: please try again later.']);
    mysqli_close($con);
    exit;
}
