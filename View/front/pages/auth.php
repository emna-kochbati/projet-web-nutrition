<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
body{
    background:#f5f7f6;
    font-family:'Segoe UI', sans-serif;
}

/* AUTH CONTAINER */
.auth-container{
    width:900px;
    height:540px;
    margin:80px auto;
    position:relative;
    overflow:hidden;
    border-radius:18px;
    background:#fff;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    display:flex;
}

/* TITRE */
.neon{
    color:#2e7d32;
    font-weight:800;
}

/* BOX FORM */
.form-box{
    position:absolute;
    width:50%;
    height:100%;
    padding:35px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    transition:0.6s ease-in-out;
}

/* LOGIN */
.signin{
    left:0;
    z-index:2;
}

/* REGISTER */
.signup{
    left:50%;
    opacity:0;
    z-index:1;
    overflow-y:auto;
}

/* ANIMATION SWITCH */
.auth-container.active .signin{
    transform:translateX(-100%);
}

.auth-container.active .signup{
    transform:translateX(0);
    opacity:1;
    z-index:5;
}

/* INPUT STYLE */
input, select{
    width:100%;
    padding:10px;
    margin:6px 0;
    border-radius:8px;
    border:1px solid #ddd;
    outline:none;
    font-size:14px;
    transition:0.3s;
}

input:focus, select:focus{
    border-color:#2e7d32;
    box-shadow:0 0 6px rgba(46,125,50,0.3);
}

/* BUTTON STYLE ECO */
button{
    width:100%;
    padding:12px;
    margin-top:10px;
    background:#2e7d32;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    color:white;
    transition:0.3s;
}

button:hover{
    background:#388e3c;
    transform:translateY(-2px);
}

/* OVERLAY (IMAGE SWITCH) */
.overlay{
    position:absolute;
    top:0;
    right:0;
    width:50%;
    height:100%;
    background:url("https://images.unsplash.com/photo-1502741338009-cac2772e18bc") center/cover;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    color:white;
    transition:0.6s ease-in-out;
    z-index:10;
}

/* MOVE ON ACTIVE */
.auth-container.active .overlay{
    right:50%;
}

/* TEXT OVERLAY */
.overlay h2{
    font-size:28px;
    margin-bottom:15px;
    text-shadow:0 2px 10px rgba(0,0,0,0.6);
}

/* SWITCH BUTTON (UNCHANGED) */
.overlay button{
    background:white;
    color:#2e7d32;
    padding:10px 20px;
    border-radius:20px;
    width:auto;
    font-weight:600;
}

/* VALIDATION */
.field-ok{
    border:2px solid #2e7d32 !important;
    box-shadow:0 0 6px rgba(46,125,50,0.3) !important;
}

.field-err{
    border:2px solid #e53935 !important;
    box-shadow:0 0 6px rgba(229,57,53,0.3) !important;
}

.err-msg{
    color:#e53935;
    font-size:11px;
    margin:4px 0;
}
</style>

<!-- ================= AUTH ================= -->
<div class="auth-container" id="auth">

    <!-- LOGIN -->
    <div class="form-box signin">

        <h2 class="neon">Connexion 🔐</h2>

        <form id="loginForm" method="POST" action="/ProjetWeb-User/index.php?url=User/login">

            <input type="text" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Mot de passe">

            <button type="submit">Se connecter</button>

        </form>

    </div>

    <!-- REGISTER -->
    <div class="form-box signup">

        <h2 class="neon">Créer un compte 🌱</h2>

        <form id="registerForm" method="POST" action="/ProjetWeb-User/index.php?url=User/register">

            <input type="text" name="nom" placeholder="Nom">
            <input type="text" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Mot de passe">

            <input type="text" name="age" placeholder="Âge">
            <input type="text" name="poids" placeholder="Poids">
            <input type="text" name="taille" placeholder="Taille">
            <input type="text" name="maladie" placeholder="Maladie (optionnel)">

            <select name="role">
                <option value="user">Utilisateur</option>
                <option value="admin">Administrateur</option>
            </select>

            <select name="objectif">
                <option value="">Objectif</option>
                <option value="Perte de poids">Perte de poids</option>
                <option value="Prise de masse">Prise de masse</option>
            </select>

            <select name="activite">
                <option value="">Niveau activité</option>
                <option value="Faible">Faible</option>
                <option value="Moyen">Moyen</option>
                <option value="Élevé">Élevé</option>
            </select>

            <button type="submit">Créer compte</button>

        </form>

    </div>

    <!-- OVERLAY (NE CHANGÉ PAS) -->
    <div class="overlay">
        <h2>EcoNutri 🌿</h2>
        <button onclick="toggle()">Switch</button>
    </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>