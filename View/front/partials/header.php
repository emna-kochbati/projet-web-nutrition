<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>EcoNutri</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="/ProjetWeb-User/assets/img/favicon.ico" rel="icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Lora:wght@600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="/ProjetWeb-User/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/ProjetWeb-User/assets/css/style.css" rel="stylesheet">
</head>

<body>

<!-- NAVBAR -->
<div class="container-fluid fixed-top px-0">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">

        <!-- LOGO -->
        <a href="/ProjetWeb-User/index.php?url=Home/index" class="navbar-brand">
            <img src="/ProjetWeb-User/assets/img/logo.png" style="height:50px;">
        </a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">

            <div class="navbar-nav ms-auto">

                <!-- HOME -->
                <a href="/ProjetWeb-User/index.php?url=Home/index" class="nav-item nav-link">
                    Home
                </a>

                <!-- USER -->
                <div class="nav-item dropdown">

                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        User
                    </a>

                    <div class="dropdown-menu">

                        <a href="/ProjetWeb-User/index.php?url=User/dashboard" class="dropdown-item">
                            Dashboard
                        </a>

                        <a href="/ProjetWeb-User/index.php?url=User/profile" class="dropdown-item">
                            Profil
                        </a>

                    </div>

                </div>

                <!-- MODULES -->
                <a href="/ProjetWeb-User/index.php?url=Home/index#about" class="nav-item nav-link">About</a>
                <a href="/ProjetWeb-User/index.php?url=Home/index#recette" class="nav-item nav-link">Recette</a>
                <a href="/ProjetWeb-User/index.php?url=Home/index#event" class="nav-item nav-link">Événement</a>
                <a href="/ProjetWeb-User/index.php?url=Home/index#sport" class="nav-item nav-link">Sport</a>
                <a href="/ProjetWeb-User/index.php?url=Home/index#contact" class="nav-item nav-link">Contact</a>
                <!-- ADMIN ICON -->
<a href="/ProjetWeb-User/index.php?url=Admin/dashboard"
   class="nav-item nav-link text-danger">

    <i class="fa fa-cog"></i> Admin
</a>

            </div>

        </div>

    </nav>

</div>
<!-- END NAVBAR -->