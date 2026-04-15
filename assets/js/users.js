function openUser(id, mode){

    console.log("CLICK OK", id, mode);

    fetch("/ProjetWeb-User/Admin/viewUser/" + id)
    .then(res => res.json())
    .then(data => {

        document.getElementById("userModal").style.display = "flex";

        document.getElementById("uid").value = data.id;
        document.getElementById("nom").value = data.nom;
        document.getElementById("email").value = data.email;
        document.getElementById("poids").value = data.poids;
        document.getElementById("taille").value = data.taille;
        document.getElementById("objectif").value = data.objectif;

        let inputs = document.querySelectorAll("#userForm input, #userForm select");

        if(mode === "view"){
            document.getElementById("modalTitle").innerText = "👁 Profil";
            inputs.forEach(e => e.disabled = true);
        }

        if(mode === "edit"){
            document.getElementById("modalTitle").innerText = "✏ Modifier";
            inputs.forEach(e => e.disabled = false);

            document.getElementById("userForm").action =
                "/ProjetWeb-User/Admin/updateUser/" + id;
        }

    })
    .catch(() => alert("Erreur chargement utilisateur"));

}

function closeModal(){
    document.getElementById("userModal").style.display = "none";
}

/* VALIDATION JS */
function validateForm(){

    let valid = true;

    let nom = document.getElementById("nom").value.trim();
    let email = document.getElementById("email").value.trim();

    document.getElementById("err_nom").innerText = "";
    document.getElementById("err_email").innerText = "";

    if(nom.length < 3){
        document.getElementById("err_nom").innerText = "Nom invalide";
        valid = false;
    }

    if(!email.includes("@")){
        document.getElementById("err_email").innerText = "Email invalide";
        valid = false;
    }

    return valid;
}