<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
body{
    background: radial-gradient(circle at top,#0f2027,#05070d 60%,#000);
    font-family: 'Open Sans', sans-serif;
}

/* CONTAINER */
.auth-container{
    width:900px;
    height:540px;
    margin:80px auto;
    position:relative;
    overflow:hidden;
    border-radius:20px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(18px);
    box-shadow: 0 0 40px rgba(0,255,153,0.15);
}

/* FORM BOX */
.form-box{
    position:absolute;
    width:50%;
    height:100%;
    padding:35px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    color:white;
    transition:0.6s ease-in-out;
}

/* LOGIN */
.signin{
    left:0;
    z-index:2;
}

/* REGISTER */
.signup{
    left:0;
    opacity:0;
    z-index:1;
    overflow-y:auto;
}

/* ACTIVE ANIMATION */
.auth-container.active .signin{
    transform:translateX(-100%);
    opacity:0;
}

.auth-container.active .signup{
    transform:translateX(100%);
    opacity:1;
    z-index:5;
}

/* INPUT */
input, select{
    width:100%;
    padding:10px;
    margin:6px 0;
    border-radius:10px;
    border:none;
    outline:none;
    font-size:14px;
}

/* BUTTON */
button{
    width:100%;
    padding:12px;
    margin-top:10px;
    background: linear-gradient(90deg,#00ff99,#00ccff);
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    color:black;
    font-size:14px;
    transition:0.3s;
}

button:hover{
    transform:scale(1.03);
}

/* OVERLAY */
.overlay{
    position:absolute;
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
}

.auth-container.active .overlay{
    right:50%;
}

.overlay h2{
    font-size:28px;
    margin-bottom:15px;
    text-shadow:0 0 10px black;
}

.overlay button{
    background:white;
    color:black;
    padding:10px 20px;
    border-radius:20px;
    width:auto;
}
</style>

<!-- ================= AUTH ================= -->
<div class="auth-container" id="auth">

    <!-- LOGIN -->
    <div class="form-box signin">

        <h2>Connexion 🔐</h2>

        <form method="POST" action="/ProjetWeb-User/User/login">

            <input type="email" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Mot de passe">

            <button type="submit">Se connecter</button>

        </form>

    </div>

    <!-- REGISTER -->
    <div class="form-box signup">

        <h2>Créer un compte 🌱</h2>
        <p style="font-size:12px; opacity:0.7;">Rejoignez EcoNutri</p>

        <form method="POST" action="/ProjetWeb-User/User/register">

            <input type="text" name="nom" placeholder="Nom">
            <input type="email" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Mot de passe">

            <input type="number" name="age" placeholder="Âge">
            <input type="number" name="poids" placeholder="Poids">
            <input type="number" name="taille" placeholder="Taille">
            <input type="text" name="maladie" placeholder="Maladie">

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

    <!-- OVERLAY -->
    <div class="overlay">
        <h2>EcoNutri 🌿</h2>
        <button onclick="toggle()">Switch</button>
    </div>

</div>

<!-- ================= JS ================= -->
<script src="/ProjetWeb-User/assets/js/auth.js"></script>

<?php include __DIR__ . '/../partials/footer.php'; ?>