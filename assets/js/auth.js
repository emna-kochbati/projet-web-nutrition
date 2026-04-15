document.addEventListener("DOMContentLoaded", function () {

    const authContainer = document.getElementById("auth");

    /* ================= SWITCH ================= */
    window.toggle = function () {
        authContainer.classList.toggle("active");
    };

    /* ================= LOGIN ================= */
    const loginForm = document.querySelector(".signin form");

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {

            const email = loginForm.email.value.trim();
            const password = loginForm.password.value.trim();

            if (email === "" || password === "") {
                e.preventDefault();
                alert("❌ Email et mot de passe obligatoires");
                return;
            }

            if (!email.includes("@")) {
                e.preventDefault();
                alert("❌ Email invalide");
                return;
            }
        });
    }

    /* ================= REGISTER ================= */
    const registerForm = document.querySelector(".signup form");

    if (registerForm) {
        registerForm.addEventListener("submit", function (e) {

            const nom = registerForm.nom.value.trim();
            const email = registerForm.email.value.trim();
            const password = registerForm.password.value.trim();
            const age = registerForm.age.value.trim();
            const poids = registerForm.poids.value.trim();
            const taille = registerForm.taille.value.trim();
            const objectif = registerForm.objectif.value;

            if (nom === "" || nom.length < 3) {
                e.preventDefault();
                alert("❌ Nom invalide (min 3 caractères)");
                return;
            }

            if (!email.includes("@") || !email.includes(".")) {
                e.preventDefault();
                alert("❌ Email invalide");
                return;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert("❌ Mot de passe trop court (min 6 caractères)");
                return;
            }

            if (age !== "" && (age < 10 || age > 100)) {
                e.preventDefault();
                alert("❌ Âge invalide (10 - 100)");
                return;
            }

            if (poids !== "" && (poids < 30 || poids > 200)) {
                e.preventDefault();
                alert("❌ Poids invalide (30kg - 200kg)");
                return;
            }

            if (taille !== "" && (taille < 100 || taille > 250)) {
                e.preventDefault();
                alert("❌ Taille invalide (100cm - 250cm)");
                return;
            }

            if (objectif === "") {
                e.preventDefault();
                alert("❌ Choisissez un objectif");
                return;
            }

        });
    }

});