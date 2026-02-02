<?php
/**
 * Header común para todas las páginas del sitio web del Club Deportivo
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sitio web oficial del Club Deportivo Andes">
    <title>Club Deportivo Andes</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/">
                        <img src="/assets/images/insignea.png" alt="Club Deportivo Andes" class="logo-image">
                        <span class="logo-text">ANDES</span>
                    </a>
                </div>
                
                <nav class="main-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="/" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === '/index.php' ? 'active' : ''; ?>">
                                Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/pages/events.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === '/pages/events.php' ? 'active' : ''; ?>">
                                Partidos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/pages/players.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === '/pages/players.php' ? 'active' : ''; ?>">
                                Jugadores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/pages/gallery.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === '/pages/gallery.php' ? 'active' : ''; ?>">
                                Galería
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/pages/contact.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === '/pages/contact.php' ? 'active' : ''; ?>">
                                Contacto
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <button class="mobile-menu-toggle" aria-label="Abrir menú">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
    
    <main class="main-content">