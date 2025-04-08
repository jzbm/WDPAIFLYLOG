document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("menu-toggle");
    const navRight = document.querySelector(".nav-right");

    toggleBtn.addEventListener("click", () => {
        navRight.classList.toggle("active");
    });
});
