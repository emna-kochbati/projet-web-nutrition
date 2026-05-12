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
    <link href="/2A35/assets/css/bootstrap.min.css" rel="stylesheet">

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
       href="/2A35/index.php?url=Home/index">
        🌿 EcoNutri
    </a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">

        <ul class="navbar-nav ms-auto align-items-center gap-2">

            <li class="nav-item">
                <a class="nav-link" href="/2A35/index.php?url=Home/index">Home</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/2A35/event">Events</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="recettefront">Recette</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">Sport</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/2A35/Restaurantfront">Restaurant</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/2A35/map">Carte</a>
            </li>

            <!-- ================= USER SESSION ================= -->
            <?php if (isset($_SESSION['user'])): ?>

                <?php 
                $userRole = strtolower(trim($_SESSION['user']['role'] ?? ''));
                if ($userRole === 'admin'): ?>

                    <!-- ADMIN -->
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold px-3"
                           style="background: #dc3545; border-radius: 20px; margin-right: 10px;"
                           href="/2A35/index.php?url=Admin/dashboard">
                            <i class="fa fa-cog"></i> Admin Panel
                        </a>
                    </li>

                <?php endif; ?>

                <!-- USER NAME (TOUS LES USERS) -->
                <li class="nav-item">
                    <a class="user-badge"
                       href="/2A35/index.php?url=User/dashboard">
                        👤 <?= htmlspecialchars($_SESSION['user']['nom']) ?>
                    </a>
                </li>

                <!-- PROFIL -->
                <li class="nav-item">
                    <a class="nav-link text-dark"
                       href="/2A35/index.php?url=User/profile">
                        Profil
                    </a>
                </li>

                <!-- LOGOUT -->
                <li class="nav-item">
                    <a class="nav-link text-dark"
                       href="/2A35/index.php?url=User/logout">
                        Logout
                    </a>
                </li>

            <?php else: ?>

                <!-- LOGIN -->
                <li class="nav-item">
                    <a class="nav-link text-primary"
                       href="/2A35/index.php?url=User/auth">
                        Login
                    </a>
                </li>

            <?php endif; ?>

        </ul>
    </div>
    <!-- Spinner End -->


    <!-- Navbar Start -->
    <div class="container-fluid fixed-top px-0 wow fadeIn" data-wow-delay="0.1s">
        <div class="top-bar row gx-0 align-items-center d-none d-lg-flex">
            <div class="col-lg-6 px-5 text-start">
                <small><i class="fa fa-map-marker-alt me-2"></i>123 Street, New York, USA</small>
                <small class="ms-4"><i class="fa fa-envelope me-2"></i>info@example.com</small>
            </div>
            <div class="col-lg-6 px-5 text-end">
                <small>Follow us:</small>
                <a class="text-body ms-3" href=""><i class="fab fa-facebook-f"></i></a>
                <a class="text-body ms-3" href=""><i class="fab fa-twitter"></i></a>
                <a class="text-body ms-3" href=""><i class="fab fa-linkedin-in"></i></a>
                <a class="text-body ms-3" href=""><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <nav class="navbar navbar-expand-lg navbar-light py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
            <a href="/2A35/Home" class="navbar-brand ms-4 ms-lg-0">
                <h1 class="fw-bold text-primary m-0">Eco<span class="text-secondary">Nutri</span></h1>
            </a>
            <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto p-4 p-lg-0">
                    <?php $currentUrl = strtolower(explode('/', trim($_GET['url'] ?? 'home', '/'))[0]); ?>
                    <a href="/2A35/Home" class="nav-item nav-link <?= $currentUrl === 'home' ? 'active' : '' ?>">Home</a>
                    <a href="/2A35/About" class="nav-item nav-link <?= $currentUrl === 'about' ? 'active' : '' ?>">About Us</a>
                    <a href="/2A35/Restaurant" class="nav-item nav-link <?= $currentUrl === 'restaurant' ? 'active' : '' ?>">Restaurants</a>
                    <a href="/2A35/Map" class="nav-item nav-link <?= $currentUrl === 'map' ? 'active' : '' ?>">🗺️ Carte</a>
                    <a href="/2A35/Product" class="nav-item nav-link <?= $currentUrl === 'product' ? 'active' : '' ?>">Products</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu m-0">
                            <a href="/2A35/Blog" class="dropdown-item <?= $currentUrl === 'blog' ? 'active' : '' ?>">Blog</a>
                            <a href="/2A35/Feature" class="dropdown-item <?= $currentUrl === 'feature' ? 'active' : '' ?>">Our Features</a>
                            <a href="/2A35/Testimonial" class="dropdown-item <?= $currentUrl === 'testimonial' ? 'active' : '' ?>">Testimonial</a>
                            <a href="/2A35/Error" class="dropdown-item">404 Page</a>
                        </div>
                    </div>
                    <a href="/2A35/Contact" class="nav-item nav-link <?= $currentUrl === 'contact' ? 'active' : '' ?>">Contact Us</a>
                </div>
                <div class="d-none d-lg-flex ms-2">
                    <a class="btn-sm-square bg-white rounded-circle ms-3" href="">
                        <small class="fa fa-search text-body"></small>
                    </a>
                    <a class="btn-sm-square bg-white rounded-circle ms-3" href="/2A35/Dashboard">
                        <small class="fa fa-user text-body"></small>
                    </a>
                    <a class="btn-sm-square bg-white rounded-circle ms-3" href="">
                        <small class="fa fa-shopping-bag text-body"></small>
                    </a>
                </div>
            </div>
        </nav>
    </div>
    <!-- Navbar End -->

<!-- SPACE -->
<div style="height:80px;"></div>
