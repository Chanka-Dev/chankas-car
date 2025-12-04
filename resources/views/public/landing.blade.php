<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Chankas Car - Taller especializado GNV en Sucre. Conversión a 3ra y 5ta generación, mantenimiento, certificación. Más de 20 años de experiencia.">
    <meta name="keywords" content="GNV Sucre, conversión GNV, 3ra generación, 5ta generación, mantenimiento GNV, certificación GNV, taller GNV Sucre">
    <title>Chankas Car - Taller GNV | Conversión y Mantenimiento</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1a3a47;
            --secondary: #2c5f73;
            --accent: #6db3c8;
            --light: #f8f9fa;
            --dark: #212529;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===== HEADER ===== */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: linear-gradient(135deg, rgba(26, 58, 71, 0.98), rgba(44, 95, 115, 0.98));
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .header.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: white;
        }

        .logo img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s ease;
        }

        .logo:hover img {
            transform: scale(1.1);
        }

        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .logo-text p {
            font-size: 0.75rem;
            opacity: 0.9;
            margin: 0;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
            list-style: none;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link:hover {
            color: var(--accent);
        }

        .btn-access {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .btn-access:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        /* Menu móvil */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* ===== HERO ===== */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(26, 58, 71, 0.95), rgba(44, 95, 115, 0.90)),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%231a3a47" width="1200" height="600"/><path fill="%232c5f73" opacity="0.3" d="M0 300L50 280C100 260 200 220 300 210C400 200 500 220 600 240C700 260 800 280 900 270C1000 260 1100 220 1150 200L1200 180V600H1150C1100 600 1000 600 900 600C800 600 700 600 600 600C500 600 400 600 300 600C200 600 100 600 50 600H0V300Z"/></svg>') center/cover;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(109, 179, 200, 0.15), transparent);
            border-radius: 50%;
            top: -200px;
            right: -200px;
            animation: float 20s infinite ease-in-out;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(109, 179, 200, 0.1), transparent);
            border-radius: 50%;
            bottom: -150px;
            left: -150px;
            animation: float 15s infinite ease-in-out reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(50px, -50px); }
        }

        .hero-content {
            text-align: center;
            color: white;
            z-index: 1;
            padding: 2rem;
            max-width: 900px;
        }

        .hero-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: white;
            color: var(--primary);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(255, 255, 255, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 2px solid white;
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }

        /* ===== SECCIONES ===== */
        section {
            padding: 5rem 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            color: var(--primary);
            position: relative;
            padding-bottom: 1rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
            border-radius: 2px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ===== SERVICIOS ===== */
        .services {
            background: var(--light);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .service-icon {
            font-size: 4rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .service-description {
            color: #666;
            line-height: 1.8;
        }

        /* ===== CARACTERÍSTICAS ===== */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateX(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 2rem;
            color: var(--accent);
            flex-shrink: 0;
        }

        .feature-text {
            flex: 1;
        }

        .feature-text h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary);
        }

        .feature-text p {
            color: #666;
            font-size: 0.95rem;
        }

        /* ===== CONTACTO ===== */
        .contact {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .contact .section-title {
            color: white;
        }

        .contact .section-title::after {
            background: white;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            text-align: center;
        }

        .contact-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-5px);
        }

        .contact-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--accent);
        }

        .contact-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .contact-info {
            font-size: 1rem;
            opacity: 0.9;
        }

        .contact-link {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-link:hover {
            color: var(--accent);
        }

        .social-links {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 1rem;
        }

        .social-link {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-link:hover {
            background: white;
            color: var(--primary);
            transform: scale(1.1);
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--dark);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-text {
            opacity: 0.8;
        }

        .admin-access {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 6px;
        }

        .admin-access:hover {
            color: var(--accent);
            background: rgba(255, 255, 255, 0.05);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-menu {
                position: fixed;
                top: 70px;
                left: -100%;
                flex-direction: column;
                background: linear-gradient(135deg, var(--primary), var(--secondary));
                width: 100%;
                padding: 2rem;
                transition: left 0.3s ease;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            }

            .nav-menu.active {
                left: 0;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .section-title {
                font-size: 2rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-icon {
                font-size: 3.5rem;
            }

            .services-grid,
            .features-grid,
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animaciones de entrada */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="header" id="header">
        <nav class="nav-container">
            <a href="/" class="logo">
                <img src="{{ asset('vendor/adminlte/dist/img/fj.png') }}" alt="Chankas Car Logo">
                <div class="logo-text">
                    <h1>Chankas Car</h1>
                    <p>Taller GNV</p>
                </div>
            </a>

            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="nav-menu" id="navMenu">
                <li><a href="#servicios" class="nav-link"><i class="fas fa-tools"></i> Servicios</a></li>
                <li><a href="#nosotros" class="nav-link"><i class="fas fa-star"></i> Nosotros</a></li>
                <li><a href="#contacto" class="nav-link"><i class="fas fa-phone"></i> Contacto</a></li>
            </ul>
        </nav>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-icon">🚗</div>
            <h1 class="hero-title">Chankas Car</h1>
            <p class="hero-subtitle">Taller GNV - Conversión y Mantenimiento Especializado</p>
            <div class="hero-buttons">
                <a href="#servicios" class="btn btn-primary">
                    <i class="fas fa-tools"></i> Ver Servicios
                </a>
                <a href="https://wa.me/59174106444?text=Hola,%20necesito%20información%20sobre%20sus%20servicios" target="_blank" class="btn btn-secondary">
                    <i class="fab fa-whatsapp"></i> Contáctanos
                </a>
            </div>
        </div>
    </section>

    <!-- SERVICIOS -->
    <section id="servicios" class="services">
        <div class="container">
            <h2 class="section-title">Nuestros Servicios</h2>
            <div class="services-grid">
                <div class="service-card fade-in">
                    <div class="service-icon">🔄</div>
                    <h3 class="service-title">Conversión 3ra Generación</h3>
                    <p class="service-description">Sistema multipunto con inyectores de gas. Instalación completa con componentes de primera calidad. Ideal para vehículos estándar.</p>
                </div>

                <div class="service-card fade-in">
                    <div class="service-icon">⚡</div>
                    <h3 class="service-title">Conversión 5ta Generación</h3>
                    <p class="service-description">Tecnología de punta con inyección directa de gas en fase líquida. Máximo rendimiento y economía para vehículos modernos y de inyección directa.</p>
                </div>

                <div class="service-card fade-in">
                    <div class="service-icon">⚙️</div>
                    <h3 class="service-title">Mantenimiento GNV</h3>
                    <p class="service-description">Revisión completa del sistema: válvulas, regulador, inyectores, sensores y cableado. Mantenimiento preventivo para máximo rendimiento.</p>
                </div>

                <div class="service-card fade-in">
                    <div class="service-icon">🔧</div>
                    <h3 class="service-title">Reparación de Sistemas GNV</h3>
                    <p class="service-description">Diagnóstico con equipos especializados. Reparación de fugas, fallas eléctricas, problemas de inyección y ajuste de mezcla.</p>
                </div>

                <div class="service-card fade-in">
                    <div class="service-icon">📋</div>
                    <h3 class="service-title">Certificación ANH</h3>
                    <p class="service-description">Certificación oficial según normativa vigente. Revisión técnica completa y documentación para circular legalmente con GNV.</p>
                </div>

                <div class="service-card fade-in">
                    <div class="service-icon">🛠️</div>
                    <h3 class="service-title">Repuestos GNV</h3>
                    <p class="service-description">Venta e instalación de repuestos originales: inyectores, reguladores, válvulas, ECU, sensores. Stock permanente de las mejores marcas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- POR QUÉ ELEGIRNOS -->
    <section id="nosotros">
        <div class="container">
            <h2 class="section-title">¿Por Qué Elegirnos?</h2>
            <div class="features-grid">
                <div class="feature-item fade-in">
                    <div class="feature-icon">⭐</div>
                    <div class="feature-text">
                        <h3>Más de 20 Años</h3>
                        <p>Dos décadas de experiencia especializada en sistemas GNV</p>
                    </div>
                </div>

                <div class="feature-item fade-in">
                    <div class="feature-icon">👨‍🔧</div>
                    <div class="feature-text">
                        <h3>Técnicos Especializados</h3>
                        <p>Personal certificado en 3ra y 5ta generación GNV</p>
                    </div>
                </div>

                <div class="feature-item fade-in">
                    <div class="feature-icon">✅</div>
                    <div class="feature-text">
                        <h3>Garantía Escrita</h3>
                        <p>Respaldo total en conversiones, reparaciones y repuestos</p>
                    </div>
                </div>

                <div class="feature-item fade-in">
                    <div class="feature-icon">💰</div>
                    <div class="feature-text">
                        <h3>Ahorro Real</h3>
                        <p>Ahorra hasta 70% en combustible con nuestras conversiones</p>
                    </div>
                </div>

                <div class="feature-item fade-in">
                    <div class="feature-icon">💻</div>
                    <div class="feature-text">
                        <h3>Atención Personalizada</h3>
                        <p>Asesoramiento experto para elegir el mejor sistema GNV</p>
                    </div>
                </div>

                <div class="feature-item fade-in">
                    <div class="feature-icon">🏆</div>
                    <div class="feature-text">
                        <h3>Repuestos Originales</h3>
                        <p>Solo componentes certificados de marcas reconocidas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACTO -->
    <section id="contacto" class="contact">
        <div class="container">
            <h2 class="section-title">Contáctanos</h2>
            <div class="contact-grid">
                <div class="contact-card fade-in">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <h3 class="contact-title">Ubicación</h3>
                    <p class="contact-info">
                        <a href="https://maps.app.goo.gl/TqrFydRpnqa8jHFJ9" target="_blank" class="contact-link">
                            Miguel Peredo 177<br>Sucre, Bolivia
                        </a>
                    </p>
                </div>

                <div class="contact-card fade-in">
                    <div class="contact-icon"><i class="fas fa-phone"></i></div>
                    <h3 class="contact-title">WhatsApp</h3>
                    <p class="contact-info">
                        <a href="https://wa.me/59174106444" target="_blank" class="contact-link">
                            +591 74106444
                        </a>
                    </p>
                </div>

                <div class="contact-card fade-in">
                    <div class="contact-icon"><i class="fas fa-clock"></i></div>
                    <h3 class="contact-title">Horarios</h3>
                    <p class="contact-info">
                        Lun - Vie: 8:00 - 12:00 | 14:00 - 18:00<br>
                        Sábados: 8:00 - 13:00
                    </p>
                </div>

                <div class="contact-card fade-in">
                    <div class="contact-icon"><i class="fas fa-share-alt"></i></div>
                    <h3 class="contact-title">Redes Sociales</h3>
                    <div class="social-links">
                        <a href="https://facebook.com/chankascar" target="_blank" class="social-link" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/chankas_car/" target="_blank" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-content">
            <p class="footer-text">© {{ date('Y') }} Chankas Car - Taller GNV. Todos los derechos reservados.</p>
            <a href="{{ route('login') }}" class="admin-access">
                <i class="fas fa-lock"></i> Acceso Administrativo
            </a>
        </div>
    </footer>

    <script>
        // Menu móvil
        const menuToggle = document.getElementById('menuToggle');
        const navMenu = document.getElementById('navMenu');
        
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            const icon = menuToggle.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Cerrar menú al hacer click en un enlace
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                menuToggle.querySelector('i').classList.add('fa-bars');
                menuToggle.querySelector('i').classList.remove('fa-times');
            });
        });

        // Header scroll effect
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Animación de entrada para elementos
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const headerHeight = document.getElementById('header').offsetHeight;
                    const targetPosition = target.offsetTop - headerHeight;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
