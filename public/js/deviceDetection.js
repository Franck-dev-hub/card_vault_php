const isTouchDevice = () => {
    return (("ontouchstart" in window) ||
        (navigator.maxTouchPoints > 0));
};

document.addEventListener("DOMContentLoaded", () => {
    if (!isTouchDevice()) {
        document.body.classList.add("not-touch-device");
    }
});
