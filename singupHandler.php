<?php
// signuphandler.php

$con = mysqli_connect("localhost", "root", "", "ldc");

if (!$con) {
  die("DB connection failed: " . mysqli_connect_error());
}

$fullname = $_POST["fullname"] ?? "";
$email    = $_POST["email"] ?? "";
$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";

// required columns in your table (NOT NULL)
$isAdmin = 0;
$phone   = 0;
$adress  = "";

// password hashing (recommended)
$hashed = password_hash($password, PASSWORD_DEFAULT);

// insert using prepared statement
$stmt = mysqli_prepare($con, "
  INSERT INTO users (FullName, Email, UserName, Password, isAdmin, phone, adress)
  VALUES (?, ?, ?, ?, ?, ?, ?)
");

mysqli_stmt_bind_param(
  $stmt,
  "sssssis",
  $fullname,
  $email,
  $username,
  $hashed,
  $isAdmin,
  $phone,
  $adress
);

if (!mysqli_stmt_execute($stmt)) {
  die("Insert error: " . mysqli_error($con));
}

mysqli_stmt_close($stmt);
mysqli_close($con);

// redirect after success
header("Location: login.html");
exit;
?>
