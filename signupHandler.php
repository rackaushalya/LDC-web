<?php
// signuphandler.php

$con = mysqli_connect("localhost", "root", "", "ldc");

if (!$con) {
  die("DB connection failed: " . mysqli_connect_error());
}

$fullname = $_POST["fullname"];
$email    = $_POST["email"];
$username = $_POST["username"];
$password = $_POST["password"];

// required columns in your table (NOT NULL)
$isAdmin = 0;
$phone   = $_POST["phone"];
$adress  = "";

// password hashing (recommended)
$hashed = password_hash($password, PASSWORD_DEFAULT);

// insert using prepared statement
$sql = mysqli_query($con, "
  INSERT INTO users (`FullName`, `Email`, `UserName`, `Password`, `isAdmin`, `phone`, `adress`)
  VALUES ('".$fullname."', '".$email."', '".$username."', '".$hashed."', '".$isAdmin."', '".$phone."', '".$adress."')
");

if (!$sql) {
  die("Insert error: " . mysqli_error($con));
}




// redirect after success
header("Location: login.html");
exit;
?>
