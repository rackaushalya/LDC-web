<?php
// signinhandler.php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: login.html");
  exit;
}

$login    = trim($_POST["login"] ?? "");     // email OR username
$password = trim($_POST["password"] ?? "");

if ($login === "" || $password === "") {
  header("Location: login.html?error=empty");
  exit;
}

$con = mysqli_connect("localhost", "root", "", "ldc");
if (!$con) {
  die("DB connection failed: " . mysqli_connect_error());
}

// Find user by Email OR Username
$sql = "SELECT * FROM users WHERE Email = ? OR UserName = ? LIMIT 1";
$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
  die("Prepare failed: " . mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, "ss", $login, $login);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
mysqli_close($con);

// Validate user + password
if (!$user) {
  header("Location: login.html?error=invalid");
  exit;
}

$hashedPassword = $user["Password"] ?? "";
if (!password_verify($password, $hashedPassword)) {
  header("Location: login.html?error=invalid");
  exit;
}

// Success: save session
$_SESSION["user"] = [
  "FullName" => $user["FullName"] ?? "",
  "Email"    => $user["Email"] ?? "",
  "UserName" => $user["UserName"] ?? "",
  "isAdmin"  => (int)($user["isAdmin"] ?? 0),
];

// Redirect after login
header("Location: index.html");
exit;
?>
