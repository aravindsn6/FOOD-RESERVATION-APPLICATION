document.addEventListener("DOMContentLoaded", function () {
    const foodAds = document.querySelector(".food-ads");
    let scrollAmount = 0;
    const scrollStep = 2; // Adjust speed
    const maxScroll = foodAds.scrollWidth;

    function autoScroll() {
        if (scrollAmount < maxScroll) {
            foodAds.style.transform = `translateX(-${scrollAmount}px)`;
            scrollAmount += scrollStep;
        } else {
            scrollAmount = 0; // Reset to start
        }
        requestAnimationFrame(autoScroll);
    }

    autoScroll(); // Start scrolling
});
