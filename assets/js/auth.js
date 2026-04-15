document.addEventListener("DOMContentLoaded", function () {

    // LOGIN FORM
    const loginForm = document.querySelector(".signin form");

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

    // REGISTER FORM
    const registerForm = document.querySelector(".signup form");

    registerForm.addEventListener("submit", function (e) {

        const nom = registerForm.nom.value.trim();
        const email = registerForm.email.value.trim();
        const password = registerForm.password.value.trim();

        if (nom === "" || email === "" || password === "") {
            e.preventDefault();
            alert("❌ Tous les champs obligatoires ne sont pas remplis");
            return;
        }

        if (!email.includes("@")) {
            e.preventDefault();
            alert("❌ Email invalide");
            return;
        }

        if (password.length < 6) {
            e.preventDefault();
            alert("❌ Mot de passe trop court (min 6 caractères)");
            return;
        }
    });

});