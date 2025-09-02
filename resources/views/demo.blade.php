<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asynchronous Digital - A Team of Awesome Developers</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #ffffff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: #fff;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #3498db;
        }

        /* Hero Section */
        .hero {
            padding: 120px 0 60px;
            text-align: center;
            background: #f8f9fa;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .hero .subtitle {
            font-size: 1.5rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .hero p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        /* Main Content */
        .section {
            padding: 80px 0;
        }

        .section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            text-align: center;
            color: #2c3e50;
        }

        /* About Section */
        .about-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .about-text p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .about-stats {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #3498db;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }

        /* Services Section */
        .services {
            background: #f8f9fa;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .service-item {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .service-item:hover {
            transform: translateY(-5px);
        }

        .service-item h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .service-item p {
            color: #666;
            line-height: 1.7;
        }

        /* Team Section */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .team-member {
            text-align: center;
            padding: 1.5rem;
        }

        .team-avatar {
            width: 100px;
            height: 100px;
            background: #3498db;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            font-weight: 700;
        }

        .team-member h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .team-member p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        /* Contact Section */
        .contact {
            background: #2c3e50;
            color: white;
        }

        .contact h2 {
            color: white;
        }

        .contact-content {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .contact p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .contact-item {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .contact-item h4 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: #ecf0f1;
        }

        .contact-item a {
            color: #3498db;
            text-decoration: none;
        }

        .contact-item a:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            background: #34495e;
            color: #ecf0f1;
            text-align: center;
            padding: 2rem 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero .subtitle {
                font-size: 1.2rem;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .section {
                padding: 60px 0;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <a href="#home" class="logo">Asynchronous Digital</a>
            <ul class="nav-links">
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#team">Team</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section id="home" class="hero">
        <div class="container">
            <h1>Asynchronous Digital</h1>
            <p class="subtitle">A team of awesome developers!</p>
            <p>We do care what we build because we love what we do! Our passion drives us to create exceptional digital solutions that make a difference.</p>
        </div>
    </section>

    <section id="about" class="section">
        <div class="container">
            <h2>About Us</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>We are a dynamic remote team of passionate developers who believe in creating meaningful digital experiences. Our expertise spans across modern web technologies, mobile development, and DevOps practices.</p>
                    
                    <p>What sets us apart is our commitment to understanding your vision and translating it into robust, scalable solutions. We don't just write code – we craft digital experiences that engage users and drive business growth.</p>
                    
                    <p>Our collaborative approach ensures that every project receives the attention it deserves, from initial concept to final deployment and beyond.</p>
                </div>
                <div class="about-stats">
                    <div class="stat-item">
                        <div class="stat-number">6</div>
                        <div class="stat-label">Team Members</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Remote</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="section services">
        <div class="container">
            <h2>What We Do</h2>
            <div class="services-grid">
                <div class="service-item">
                    <h3>PHP & Laravel Development</h3>
                    <p>Building robust backend systems with PHP and Laravel framework. We create scalable APIs, web applications, and custom solutions that power your business operations efficiently.</p>
                </div>
                <div class="service-item">
                    <h3>React & JavaScript</h3>
                    <p>Crafting interactive and responsive frontend experiences using React and modern JavaScript. We build user interfaces that are both beautiful and functional.</p>
                </div>
                <div class="service-item">
                    <h3>Flutter & Mobile Development</h3>
                    <p>Cross-platform mobile applications that work seamlessly on both iOS and Android. We leverage Flutter's power to deliver native performance with a single codebase.</p>
                </div>
                <div class="service-item">
                    <h3>WordPress Solutions</h3>
                    <p>Custom WordPress themes, plugins, and optimizations. We transform your content management needs into streamlined, user-friendly experiences.</p>
                </div>
                <div class="service-item">
                    <h3>DevOps & Cloud</h3>
                    <p>Streamlined deployment processes, CI/CD pipelines, and cloud infrastructure management. We ensure your applications run smoothly and scale effortlessly.</p>
                </div>
                <div class="service-item">
                    <h3>Full-Stack Consulting</h3>
                    <p>From concept to deployment, we provide end-to-end consulting services. Our expertise guides you through technology decisions and implementation strategies.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="team" class="section">
        <div class="container">
            <h2>Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="team-avatar">AD</div>
                    <h3>Alex Developer</h3>
                    <p>Laravel Specialist</p>
                </div>
                <div class="team-member">
                    <div class="team-avatar">JS</div>
                    <h3>Jordan Smith</h3>
                    <p>React Developer</p>
                </div>
                <div class="team-member">
                    <div class="team-avatar">MC</div>
                    <h3>Morgan Chen</h3>
                    <p>Flutter Developer</p>
                </div>
                <div class="team-member">
                    <div class="team-avatar">RJ</div>
                    <h3>Riley Johnson</h3>
                    <p>DevOps Engineer</p>
                </div>
                <div class="team-member">
                    <div class="team-avatar">SP</div>
                    <h3>Sam Patel</h3>
                    <p>WordPress Specialist</p>
                </div>
                <div class="team-member">
                    <div class="team-avatar">TK</div>
                    <h3>Taylor Kim</h3>
                    <p>Mobile Developer</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="section contact">
        <div class="container">
            <div class="contact-content">
                <h2>Let's Work Together</h2>
                <p>Ready to bring your digital vision to life? We'd love to hear about your project and discuss how our expertise can help you achieve your goals.</p>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <h4>Email</h4>
                        <a href="mailto:hello@asynchronousdigital.com">hello@asynchronousdigital.com</a>
                    </div>
                    <div class="contact-item">
                        <h4>Website</h4>
                        <a href="https://asynchronousdigital.com">asynchronousdigital.com</a>
                    </div>
                    <div class="contact-item">
                        <h4>Phone</h4>
                        <a href="tel:+15124213940">+1 (512) 421-3940</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2025 Asynchronous Digital. All rights reserved. | Remote Team, Global Impact</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Simple scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Add animation to service items and team members
        document.querySelectorAll('.service-item, .team-member').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>