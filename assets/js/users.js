document.addEventListener("DOMContentLoaded", function () {

    window.openUser = function (user, mode) {

        document.getElementById("userModal").classList.add("show");

        document.getElementById("uid").value = user.id;
        document.getElementById("nom").value = user.nom;
        document.getElementById("email").value = user.email;
        document.getElementById("poids").value = user.poids;
        document.getElementById("taille").value = user.taille;
        document.getElementById("objectif").value = user.objectif;
        document.getElementById("status").value = user.status;

        let inputs = document.querySelectorAll("#userForm input, #userForm select");
        let saveBtn = document.getElementById("saveBtn");

        if (mode === "view") {
            inputs.forEach(i => i.disabled = true);
            saveBtn.style.display = "none";
        }

        if (mode === "edit") {
            inputs.forEach(i => i.disabled = false);
            saveBtn.style.display = "block";

            document.getElementById("userForm").action =
                "/ProjetWeb-User/index.php?url=Admin/updateUser/" + user.id;
        }
    };

    window.closeModal = function () {
        document.getElementById("userModal").classList.remove("show");
    };

    function setError(input, condition) {
        if (condition) input.classList.add("error");
        else input.classList.remove("error");
    }

    function isNumber(v) {
        return v !== "" && !isNaN(v);
    }

    const form = document.getElementById("userForm");

    if (form) {
        form.addEventListener("submit", function (e) {

            let valid = true;

            let nom = document.getElementById("nom");
            let email = document.getElementById("email");
            let poids = document.getElementById("poids");
            let taille = document.getElementById("taille");

            if (nom.value.length < 3) {
                setError(nom, true);
                valid = false;
            } else setError(nom, false);

            if (!email.value.includes("@")) {
                setError(email, true);
                valid = false;
            } else setError(email, false);

            if (!isNumber(poids.value) || poids.value < 30) {
                setError(poids, true);
                valid = false;
            } else setError(poids, false);

            if (!isNumber(taille.value) || taille.value < 100) {
                setError(taille, true);
                valid = false;
            } else setError(taille, false);

            if (!valid) e.preventDefault();
        });
    }

});