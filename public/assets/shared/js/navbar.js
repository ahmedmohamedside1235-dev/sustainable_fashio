// =====================
// AUTO ACTIVE NAVBAR
// =====================
(function () {
    let page = window.location.pathname.split("/").pop() || "index.html";
    document.querySelectorAll(".navbar-nav .nav-link").forEach(function (link) {
        link.classList.remove("active");
        let href = (link.getAttribute("href") || "").split("/").pop();
        if (href && href !== "#" && href === page) {
            link.classList.add("active");
        }
    });
})();
