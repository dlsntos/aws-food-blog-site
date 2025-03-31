fetch("../includes/check_login.php", {
  credentials: "include",
})
  .then((response) => response.json())
  .then((data) => {
    if (!data.logged_in) {
      window.location.href = "../public/login.php";
    }
  })
  .catch((error) => {
    console.error("Error checking login:", error);
    
  });
