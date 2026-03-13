// ============================
// SWITCH BETWEEN FORMS
// ============================
const loginForm = document.getElementById("loginForm");
const signupForm = document.getElementById("signupForm");
const showSignup = document.getElementById("showSignup");
const showLogin = document.getElementById("showLogin");

showSignup.addEventListener("click", (e) => {
  e.preventDefault();
  loginForm.classList.remove("active");
  signupForm.classList.add("active");
});

showLogin.addEventListener("click", (e) => {
  e.preventDefault();
  signupForm.classList.remove("active");
  loginForm.classList.add("active");
});

// ============================
// SHOW / HIDE PASSWORD
// ============================
function togglePassword(inputId, iconElement) {
  const input = document.getElementById(inputId);

  if (input.type === "password") {
    input.type = "text";
    iconElement.classList.replace("fa-eye", "fa-eye-slash");
  } else {
    input.type = "password";
    iconElement.classList.replace("fa-eye-slash", "fa-eye");
  }
}

// ============================
// SIGNUP FORM HANDLER
// ============================
signupForm.addEventListener("submit", (e) => {
  e.preventDefault();

  const name = signupForm.querySelector('input[placeholder="Full Name"]').value;
  const email = signupForm.querySelector('input[type="email"]').value;
  const password = signupForm.querySelector("#signupPassword").value;
  const contact = signupForm.querySelector('input[placeholder="Contact Number"]').value;
  const address = signupForm.querySelector('input[placeholder="Address"]').value;
  const gender = document.getElementById("genderSelect").value;

  if (!gender) {
    alert("Please select your gender!");
    return;
  }

  console.log("Signup Data:", { name, email, password, contact, address, gender });
  alert(`Account created successfully!\nWelcome, ${name}!`);

  signupForm.reset();
  signupForm.classList.remove("active");
  loginForm.classList.add("active");
});

// ============================
// LOGIN FORM HANDLER
// ============================
loginForm.addEventListener("submit", (e) => {
  e.preventDefault();
  const usernameOrEmail = loginForm.querySelector('input[type="text"]').value;
  const password = loginForm.querySelector("#loginPassword").value;

  console.log("Login Data:", { usernameOrEmail, password });
  alert("Logged in successfully!");
});


// login data !!!

if (result.success) {
  // Store the logged-in user's email
  sessionStorage.setItem("userEmail", result.user.email);
  // Redirect to profile page
  window.location.href = "profile.html";
}
