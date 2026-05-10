<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>EcoNutri</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="/ProjetWeb-User/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <style>
        .navbar-wow {
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.9);
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        .nav-link {
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-link:hover {
            transform: translateY(-2px);
            color: #00c853 !important;
        }

        .admin-badge {
            background: linear-gradient(45deg, #ff1744, #d50000);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        .user-badge {
            background: linear-gradient(45deg, #00c853, #64dd17);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            text-decoration: none;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-wow fixed-top px-4">

    <!-- LOGO -->
    <a class="navbar-brand fw-bold text-success"
       href="/ProjetWeb-User/index.php?url=Home/index">
        🌿 EcoNutri
    </a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">

        <ul class="navbar-nav ms-auto align-items-center gap-2">

            <li class="nav-item">
                <a class="nav-link" href="/ProjetWeb-User/index.php?url=Home/index">Home</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">About</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">Recette</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">Sport</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">Contact</a>
            </li>

            <!-- ================= USER SESSION ================= -->
            <?php if (isset($_SESSION['user'])): ?>

                <?php if ($_SESSION['user']['role'] === 'admin'): ?>

                    <!-- ADMIN -->
                    <li class="nav-item">
                        <a class="nav-link text-danger fw-bold"
                           href="/ProjetWeb-User/index.php?url=Admin/dashboard">
                            <i class="fa fa-cog"></i> Admin Panel
                        </a>
                    </li>

                <?php endif; ?>

                <!-- USER NAME (TOUS LES USERS) -->
                <li class="nav-item">
                    <a class="user-badge"
                       href="/ProjetWeb-User/index.php?url=User/dashboard">
                        👤 <?= htmlspecialchars($_SESSION['user']['nom']) ?>
                    </a>
                </li>

                <!-- PROFIL -->
                <li class="nav-item">
                    <a class="nav-link text-dark"
                       href="/ProjetWeb-User/index.php?url=User/profile">
                        Profil
                    </a>
                </li>

                <!-- LOGOUT -->
                <li class="nav-item">
                    <a class="nav-link text-dark"
                       href="/ProjetWeb-User/index.php?url=User/logout">
                        Logout
                    </a>
                </li>

            <?php else: ?>

                <!-- LOGIN -->
                <li class="nav-item">
                    <a class="nav-link text-primary"
                       href="/ProjetWeb-User/index.php?url=User/login">
                        Login
                    </a>
                </li>

            <?php endif; ?>

        </ul>
    </div>
</nav>

<!-- SPACE -->
<div style="height:80px;"></div>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>EcoNutri</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="/ProjetWeb-User/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <style>
        .navbar-wow {
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.9);
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        .nav-link {
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-link:hover {
            transform: translateY(-2px);
            color: #00c853 !important;
        }

        .admin-badge {
            background: linear-gradient(45deg, #ff1744, #d50000);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        .user-badge {
            background: linear-gradient(45deg, #00c853, #64dd17);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light navbar-wow fixed-top px-4">

    <!-- LOGO -->
    <a class="navbar-brand fw-bold text-success"
       href="/ProjetWeb-User/index.php?url=Home/index">
        🌿 EcoNutri
    </a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">

        <!-- LINKS -->
        <ul class="navbar-nav ms-auto align-items-center gap-2">

            <li class="nav-item">
                <a class="nav-link" href="/ProjetWeb-User/index.php?url=Home/index">Home</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">About</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">Recette</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">Sport</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">Contact</a>
            </li>

            <!-- ================= USER SESSION ================= -->
            <?php if (isset($_SESSION['user'])): ?>

                <?php if ($_SESSION['user']['role'] === 'admin'): ?>

                    <!-- ADMIN -->
                    <li class="nav-item">
                        <a class="nav-link text-danger fw-bold"
                           href="/ProjetWeb-User/index.php?url=Admin/dashboard">
                            <i class="fa fa-cog"></i> Admin Panel
                        </a>
                    </li>

                    <li class="nav-item">
                        <span class="admin-badge">
                            👑 ADMIN
                        </span>
                    </li>

                <?php else: ?>

                    <!-- USER -->
                    <li class="nav-item">
                        <span class="user-badge">
                            👤 <?= htmlspecialchars($_SESSION['user']['nom']) ?>
                        </span>
                    </li>

                <?php endif; ?>

                <!-- LOGOUT -->
                <li class="nav-item">
                    <a class="nav-link text-dark"
                       href="/ProjetWeb-User/index.php?url=User/logout">
                        Logout
                    </a>
                </li>

            <?php else: ?>

                <!-- LOGIN -->
                <li class="nav-item">
                    <a class="nav-link text-primary"
                       href="/ProjetWeb-User/index.php?url=User/login">
                        Login
                    </a>
                </li>

            <?php endif; ?>

        </ul>
    </div>
</nav>

<!-- SPACE -->
<div style="height:80px;"></div>