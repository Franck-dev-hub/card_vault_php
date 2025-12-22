function toggleMenu() {
    const overlay = document.getElementById("menuOverlay");
    const backdrop = document.getElementById("menuBackdrop");
    overlay.classList.toggle("open");
    backdrop.classList.toggle("open");
}
