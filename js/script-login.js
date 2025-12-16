document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const username = document.getElementById("username");
  const password = document.getElementById("password");
  const errorMessage = document.getElementById("error-message");

  // Show server-side errors from signinhandler.php (?error=...)
  const params = new URLSearchParams(window.location.search);
  const err = params.get("error");

  if (err) {
    let msg = "Login failed. Try again.";
    if (err === "empty") msg = "Please fill in both fields.";
    if (err === "invalid") msg = "Invalid username/email or password.";

    errorMessage.innerText = msg;
    errorMessage.style.display = "block";
  } else {
    errorMessage.style.display = "none";
  }

  form.addEventListener("submit", (e) => {
    let errors = [];

    const loginValue = username.value.trim();
    const passValue = password.value.trim();

    // Validate login
    if (loginValue === "") {
      errors.push("Please enter your email or username.");
    } else if (loginValue.includes("@")) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(loginValue)) {
        errors.push("Please enter a valid email address.");
      }
    }

    // Validate password
    if (passValue === "") {
      errors.push("Please enter your password.");
    } else if (passValue.length < 8) {
      errors.push("Password must be at least 8 characters long.");
    }

    // If errors -> stop submit
    if (errors.length > 0) {
      e.preventDefault();
      errorMessage.innerText = errors.join(" ");
      errorMessage.style.display = "block";
      return;
    }

    // If ok -> allow submit to signinhandler.php
    errorMessage.style.display = "none";
  });
});
