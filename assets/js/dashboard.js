document.addEventListener("DOMContentLoaded", function () {
    const navToggles = document.querySelectorAll(".nav-toggle");

    navToggles.forEach(function(toggle) {
        const parentSection = toggle.closest('.nav-section');
        const subMenu = toggle.nextElementSibling;
        
        // If the menu is active, expand it automatically on load
        if (parentSection && parentSection.classList.contains('active') && subMenu) {
            subMenu.style.maxHeight = subMenu.scrollHeight + "px";
        }

        toggle.addEventListener("click", function () {
            // Optional: Close all other open sub-menus
            document.querySelectorAll(".sub-menu").forEach(function(menu) {
                if (menu !== subMenu) {
                    menu.style.maxHeight = "0";
                }
            });

            // Toggle the current submenu
            if (subMenu.style.maxHeight && subMenu.style.maxHeight !== "0px") {
                subMenu.style.maxHeight = "0";
            } else {
                subMenu.style.maxHeight = subMenu.scrollHeight + "px";
            }
        });
    });
});
