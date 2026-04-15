<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
body{
    background:#0b0f1a;
    color:white;
    font-family: 'Segoe UI', sans-serif;
}

/* TEXT CLEAN */
h1,h2,h3,h4,p{
    color:white;
}

/* HERO */
.hero{
    text-align:center;
    padding:100px 20px;
    background: radial-gradient(circle at top,#111827,#0b0f1a);
}

.hero h1{
    font-size:54px;
    font-weight:900;
}

.hero img{
    width:260px;
    margin-top:25px;
    border-radius:20px;
    box-shadow:0 0 35px rgba(0,255,120,0.3);
}

/* SECTION */
.section{
    padding:80px 10%;
}

/* TITLES */
.section h2{
    margin-bottom:25px;
    color:#00e676;
    font-weight:700;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.06);
    padding:20px;
    border-radius:15px;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-10px);
    box-shadow:0 0 25px rgba(0,255,120,0.25);
}

.card img{
    width:100%;
    border-radius:12px;
    margin-bottom:10px;
}

/* BUTTON */
.btn{
    padding:12px 20px;
    background:#00e676;
    color:black;
    border-radius:10px;
    font-weight:bold;
    text-decoration:none;
}
</style>

<!-- HERO -->
<div class="hero" id="home">
    <h1>🌿 EcoNutri</h1>
    <p>Nutrition intelligente • Sport • Bien-être</p>

    <img src="https://images.unsplash.com/photo-1490818387583-1baba5e638af" alt="EcoNutri">

    <br><br>
    <a class="btn" href="#about">Découvrir EcoNutri</a>
</div>

<!-- ABOUT -->
<div class="section" id="about">
    <h2>À propos d’EcoNutri</h2>
    <p>
        EcoNutri est une plateforme intelligente qui combine nutrition, sport et santé.
        Nous vous aidons à améliorer votre mode de vie avec des conseils simples et efficaces.
    </p>
</div>

<!-- SERVICES -->
<div class="section" id="services">
    <h2>🔥 Nos services</h2>

    <div class="grid">

        <div class="card">
            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c">
            <h3>🍽️ Recettes</h3>
            <p>Repas sains et équilibrés</p>
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438">
            <h3>🏋️ Sport</h3>
            <p>Programmes adaptés à tous niveaux</p>
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1506784365847-bbad939e9335">
            <h3>📅 Événements</h3>
            <p>Marathons, conférences, challenges</p>
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1555992336-03a23c7b20ee">
            <h3>🤝 Partenaires</h3>
            <p>Restaurants & associations santé</p>
        </div>

    </div>
</div>

<!-- RECETTES -->
<div class="section" id="recette">
    <h2>🍽️ Recettes populaires</h2>

    <div class="grid">

        <div class="card">
            <img src="https://images.unsplash.com/photo-1552332386-f8dd00dc2f85">
            🥗 Salade healthy
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1553787499-6f913a6f7f0c">
            🥤 Jus detox
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1604908177522-040e9c5b3c2f">
            🍗 Repas équilibré
        </div>

    </div>
</div>

<!-- SPORT & EVENT -->
<div class="section" id="event">
    <h2>🏋️ Sport & 📅 Événements</h2>

    <div class="grid">

        <div class="card">
            <img src="https://images.unsplash.com/photo-1517963879433-6ad2b056d712">
            Fitness & Musculation
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773">
            Yoga & Relaxation
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1526401485004-2aa6b05a4d2e">
            Marathon Santé
        </div>

        <div class="card">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f">
            Conférences Nutrition
        </div>

    </div>
</div>

<!-- AVIS -->
<div class="section" id="avis">
    <h2>💬 Avis utilisateurs</h2>

    <div class="grid">

        <div class="card">
            ⭐⭐⭐⭐⭐<br>
            Très bonne plateforme pour améliorer mon alimentation
        </div>

        <div class="card">
            ⭐⭐⭐⭐⭐<br>
            Les conseils sport sont simples et efficaces
        </div>

    </div>
</div>

<!-- CONSEIL -->
<div class="section" id="contact">
    <h2>💡 Conseil du jour</h2>

    <div class="card">
        💧 Boire 2L d’eau par jour améliore fortement l’énergie et la concentration.
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>