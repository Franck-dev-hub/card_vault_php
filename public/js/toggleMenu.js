function toggleMenu(menuType = "burger") {
    if (menuType === "burger") {
        const overlay = document.getElementById("menuOverlay");
        const backdrop = document.getElementById("menuBackdrop");
        overlay.classList.toggle("open");
        backdrop.classList.toggle("open");
    } else if (menuType === "profile") {
        const overlay = document.getElementById("profileOverlay");
        const backdrop = document.getElementById("profileBackdrop");
        overlay.classList.toggle("open");
        backdrop.classList.toggle("open");
    }
}
