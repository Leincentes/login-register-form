document.addEventListener("DOMContentLoaded", function() {
  const form = document.querySelector(".login form");

  form.addEventListener("submit", function(event) {
    event.preventDefault();

    const formData = new FormData(form);

    const timeoutPromise = new Promise((resolve, reject) => {
      setTimeout(() => {
        reject(new Error("Request timed out"));
      }, 1000);
    });

    const fetchPromise = fetch("/login/user", {
      method: "POST",
      body: formData
    });

    Promise.race([fetchPromise, timeoutPromise])
      .then(response => {
        if (response.ok) {
          window.location.href = "/home";
        } else {
          return response.text().then(error => {
            throw new Error(error);
          });
        }
      })
      .catch(error => {
        const errorText = document.querySelector(".error-text");
        if (error instanceof Error && error.message === "Request timed out") {
          errorText.textContent = "Request timed out. Please try again.";
        } else if (error instanceof TypeError) {
          errorText.textContent = "Network error occurred. Please try again.";
        } else {
          errorText.textContent = "Server error occurred. Please try again.";
        }
        errorText.style.display = "block";
      });
  });
});
