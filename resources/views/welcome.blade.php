<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>HeartFit — Hidup Sehat dengan Makanan Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --hf-primary: #0d6efd;
            --hf-primary-dark: #0a58ca;
            --hf-bg-warm: #f8f9fa;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
        }

        /* Animated reveal on scroll */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease-out, transform 0.7s ease-out;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* Navbar scroll effect */
        .navbar {
            transition: box-shadow 0.3s ease, background-color 0.3s ease;
        }
        .navbar.scrolled {
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.08);
        }

        /* Hero pulse ring */
        .hero-badge {
            animation: pulse-ring 2.5s ease-out infinite;
            display: inline-block;
        }
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.35); }
            70% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
            100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
        }

        /* Floating shapes in hero */
        .hero-shapes .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.12;
            background: var(--hf-primary);
            animation: float 8s ease-in-out infinite;
        }
        .hero-shapes .shape:nth-child(1) {
            width: 120px; height: 120px;
            top: 10%; left: -40px;
            animation-delay: 0s;
        }
        .hero-shapes .shape:nth-child(2) {
            width: 80px; height: 80px;
            top: 60%; left: 70%;
            animation-delay: 2s;
        }
        .hero-shapes .shape:nth-child(3) {
            width: 50px; height: 50px;
            top: 30%; left: 60%;
            animation-delay: 4s;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-25px) rotate(15deg); }
        }

        /* Stats hover */
        .stat-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            cursor: default;
        }
        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.10) !important;
        }

        /* Card hover on menu / packages */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12) !important;
        }

        /* CTA glow on scroll */
        .btn-glow {
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease;
        }
        .btn-glow::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }
        .btn-glow:hover::after {
            transform: translateX(100%);
        }

        /* Active nav link indicator */
        .nav-link.active-nav {
            font-weight: 700;
            color: var(--hf-primary) !important;
            position: relative;
        }
        .nav-link.active-nav::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0.5rem;
            right: 0.5rem;
            height: 2px;
            background: var(--hf-primary);
            border-radius: 2px;
        }

        /* Scroll progress bar */
        #scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: var(--hf-primary);
            width: 0%;
            z-index: 1055;
            transition: width 0.1s linear;
        }

        /* Back-to-top */
        #back-to-top {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--hf-primary);
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            opacity: 0;
            transform: translateY(12px);
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease, background 0.2s;
            z-index: 1050;
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.35);
        }
        #back-to-top.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
        #back-to-top:hover {
            background: var(--hf-primary-dark);
        }

        /* About image parallax-lite */
        .about-img-wrap {
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }
        .about-img-wrap img {
            transition: transform 0.6s ease;
        }
        .about-img-wrap:hover img {
            transform: scale(1.04);
        }

        /* Pricing ribbon shimmer */
        .ribbon {
            position: relative;
            overflow: hidden;
        }
        .ribbon::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transform: skewX(-20deg);
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 200%; }
        }

        /* Footer wave divider feel */
        footer {
            position: relative;
        }
        footer::before {
            content: '';
            position: absolute;
            top: -40px;
            left: 0;
            right: 0;
            height: 40px;
            background: inherit;
            clip-path: ellipse(55% 100% at 50% 100%);
            opacity: 0.08;
        }
    </style>
</head>

<body class="bg-body-tertiary">

    <div id="scroll-progress"></div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
                <img src="{{ asset('assets/img/favicon/heartfit_logo.png') }}" width="200px" alt="">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item"><a class="nav-link active-nav" href="#hero">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#paket">Paket</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="/login" class="btn btn-outline-primary">Login</a>
                    <a href="{{ url('registrasi') }}" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section id="hero" class="py-5 position-relative overflow-hidden">
        <div class="hero-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        <div class="container py-4 position-relative">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <span class="hero-badge badge text-bg-light border px-3 py-2">Makanan Sehat</span>
                    <h1 class="display-5 fw-bold mt-3 reveal">
                        Mulai Hari dengan <span class="text-primary">Pilihan Makanan Sehat</span>
                    </h1>
                    <p class="lead text-secondary mt-3 reveal reveal-delay-1">
                        HeartFit membantu Anda menemukan inspirasi menu sehat, menghitung kalori,
                        dan memantau kebiasaan makan agar jantung tetap kuat dan tubuh lebih bugar.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4 reveal reveal-delay-2">
                        <a href="/login" class="btn btn-primary btn-lg btn-glow">
                            <i class="bi bi-basket"></i> Mulai Sekarang
                        </a>
                        <a href="#about" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-info-circle"></i> Pelajari Lebih Lanjut
                        </a>
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-6 col-md-4">
                            <div class="card border-0 shadow-sm stat-card reveal reveal-delay-1">
                                <div class="card-body">
                                    <div class="small text-secondary">Kalori Harian</div>
                                    <div class="h4 mb-0 text-primary" data-count="1850" data-suffix=" kcal">0 kcal</div>
                                    <div class="small text-primary"><i class="bi bi-arrow-up-right"></i> Ideal</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="card border-0 shadow-sm stat-card reveal reveal-delay-2">
                                <div class="card-body">
                                    <div class="small text-secondary">Porsi Sayur</div>
                                    <div class="h4 mb-0 text-primary" data-count="5" data-suffix="x">0x</div>
                                    <div class="small text-primary"><i class="bi bi-check-circle"></i> Sesuai</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card border-0 shadow-sm stat-card reveal reveal-delay-3">
                                <div class="card-body">
                                    <div class="small text-secondary">Minum Air</div>
                                    <div class="h4 mb-0 text-primary" data-count="2.5" data-suffix=" L" data-decimal="1">0 L</div>
                                    <div class="small text-primary"><i class="bi bi-droplet-half"></i> Cukup</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 text-center reveal">
                    <img src="{{ asset('assets/img/logo/slhs.png') }}" class="img-fluid rounded shadow"
                        alt="Healthy Food">
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Makanan -->
    <section id="paket" class="py-5 bg-white border-top">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <h3 class="fw-bold text-primary">Menu Makanan</h3>
                <p class="text-secondary mb-0">Menu Makanan Yang Akan Didapatkan</p>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 hover-lift reveal reveal-delay-1">
                        <img class="card-img-top object-fit-cover" style="height:400px;" src="{{ asset('assets/img/menus/1.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Paket Diet Sehat Rendah Kalori</h5>
                            <p class="card-text text-secondary">
                                Menu diet lengkap dengan porsi seimbang karbohidrat, protein, dan sayuran segar. Cocok untuk menjaga berat badan tetap ideal.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 hover-lift reveal reveal-delay-2">
                        <img class="card-img-top object-fit-cover" style="height:400px;" src="{{ asset('assets/img/menus/2.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Lunch Box Diet Praktis</h5>
                            <p class="card-text text-secondary">
                                Hidangan diet bernutrisi dengan buah dan sayuran segar, dikombinasikan dengan lauk sehat rendah lemak untuk energi sepanjang hari.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 hover-lift reveal reveal-delay-3">
                        <img class="card-img-top object-fit-cover" style="height:400px;" src="{{ asset('assets/img/menus/3.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Healthy Diet Bento</h5>
                            <p class="card-text text-secondary">
                                Sajian ala bento diet dengan porsi terkontrol, kaya serat dan protein, mendukung program diet tanpa rasa lapar.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 hover-lift reveal reveal-delay-1">
                        <img class="card-img-top object-fit-cover" style="height:400px;" src="{{ asset('assets/img/menus/4.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Paket Makan Malam Diet</h5>
                            <p class="card-text text-secondary">
                                Makanan diet rendah kalori dengan kombinasi lauk sehat, sayuran hijau, dan buah segar yang cocok untuk malam hari.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 hover-lift reveal reveal-delay-2">
                        <img class="card-img-top object-fit-cover" style="height:400px;" src="{{ asset('assets/img/menus/5.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Diet Box Premium</h5>
                            <p class="card-text text-secondary">
                                Menu diet premium dengan variasi makanan sehat, dibuat untuk menjaga metabolisme tubuh tetap optimal setiap hari.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PAKET (versi biru) -->
    <section id="paket" class="py-5 bg-white border-top">
        <div class="container">
            <div class="text-center mb-4 reveal">
                <h3 class="fw-bold text-primary">HeartFit Diet Reguler</h3>
                <p class="text-secondary mb-0">Pilihan paket makan sehat untuk kebutuhan harian Anda.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-1">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Reguler</h5>
                            <div class="display-6 fw-bold mt-2">Rp 50.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">Menu seimbang</li>
                                <li class="list-group-item">Pilihan karbo sehat</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-2">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Mingguan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 400.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">4 hari × 2 kali makan</li>
                                <li class="list-group-item">8 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-3">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Bulanan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 1.180.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">12 hari × 2 kali makan</li>
                                <li class="list-group-item">24 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-4">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">3 Bulanan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 3.540.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">36 hari × 2 kali makan</li>
                                <li class="list-group-item">72 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center my-5 reveal">
                <h3 class="fw-bold text-primary">HeartFit Diet Premium</h3>
                <p class="text-secondary mb-0">Pilihan premium untuk hasil lebih cepat & fleksibel.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-1 ribbon">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Ekspress</h5>
                            <div class="display-6 fw-bold mt-2">Rp 170.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">2 kali makan (siang & malam)</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-2">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Mingguan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 650.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">4 hari × 2 kali makan</li>
                                <li class="list-group-item">8 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-3">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Bulanan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 1.950.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">12 hari × 2 kali makan</li>
                                <li class="list-group-item">24 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-4">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">3 Bulanan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 5.830.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">36 hari × 2 kali makan</li>
                                <li class="list-group-item">72 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info border mt-4 mb-0 reveal" role="alert">
                <i class="bi bi-info-circle"></i>
                <span class="ms-2 small">
                    Harga dapat berubah sewaktu-waktu. Jadwal pengantaran & preferensi menu dapat diatur setelah
                    checkout.
                </span>
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="py-5 bg-white border-top">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold text-primary reveal">Tentang HeartFit</h2>
                    <p class="text-secondary mt-3 reveal reveal-delay-1">
                        HeartFit adalah layanan catering sehat dari Departemen Instalasi Gizi Kementerian Kesehatan RSJPD Harapan Kita yang menghadirkan menu bergizi, seimbang, dan berkualitas. Setiap hidangan disusun berdasarkan prinsip gizi yang tepat serta diolah secara higienis untuk mendukung pola hidup sehat dan membantu menjaga kesehatan, khususnya kesehatan jantung.
                    </p>
                    <ul class="list-unstyled mt-3">
                        <li class="d-flex align-items-start mb-3 reveal reveal-delay-1">
                            <i class="bi bi-check2-circle text-primary me-3 mt-1"></i>
                            <span>Menu bergizi dengan kualitas terjamin</span>
                        </li>
                        <li class="d-flex align-items-start mb-3 reveal reveal-delay-2">
                            <i class="bi bi-check2-circle text-primary me-3 mt-1"></i>
                            <span>Disusun oleh tenaga gizi profesional</span>
                        </li>
                        <li class="d-flex align-items-start reveal reveal-delay-3">
                            <i class="bi bi-check2-circle text-primary me-3 mt-1"></i>
                            <span>Mendukung pola makan sehat dan seimbang</span>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 reveal reveal-delay-2">
                    <div class="about-img-wrap">
                        <img src="https://picsum.photos/500/350?hospital" class="img-fluid" alt="About HeartFit">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR -->
    <section id="fitur" class="py-5">
        <div class="container">
            <div class="text-center mb-4 reveal">
                <h3 class="fw-bold text-primary">Fitur Utama</h3>
                <p class="text-secondary mb-0">Makan lebih sehat, hidup lebih bertenaga.</p>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-1">
                        <div class="card-body text-center">
                            <i class="bi bi-basket2 fs-1 text-primary mb-3"></i>
                            <h5 class="mt-2">Inspirasi Resep</h5>
                            <p class="text-secondary mb-0">Ribuan ide makanan sehat praktis untuk setiap hari.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-2">
                        <div class="card-body text-center">
                            <i class="bi bi-bar-chart-line fs-1 text-primary mb-3"></i>
                            <h5 class="mt-2">Tracking Nutrisi</h5>
                            <p class="text-secondary mb-0">Hitung kalori, protein, karbohidrat, dan vitamin.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg h-100 hover-lift reveal reveal-delay-3">
                        <div class="card-body text-center">
                            <i class="bi bi-bell fs-1 text-primary mb-3"></i>
                            <h5 class="mt-2">Reminder Sehat</h5>
                            <p class="text-secondary mb-0">Ingatkan waktu makan, minum air, dan konsumsi buah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-top">
        <div class="container py-4">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-heart-fill text-primary"></i>
                    <span class="fw-semibold">HeartFit</span>
                </div>
                <div class="small text-secondary">© 2026 DIO | HEARTFIT</div>
            </div>
        </div>
    </footer>

    <button id="back-to-top" aria-label="Back to top">
        <i class="bi bi-chevron-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const navbar = document.querySelector('.navbar');
            const progress = document.getElementById('scroll-progress');
            const backToTop = document.getElementById('back-to-top');
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('section[id]');

            function updateScroll() {
                const scrollTop = window.scrollY;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

                progress.style.width = pct + '%';

                if (scrollTop > 60) {
                    navbar.classList.add('scrolled');
                    backToTop.classList.add('show');
                } else {
                    navbar.classList.remove('scrolled');
                    backToTop.classList.remove('show');
                }

                let current = '';
                sections.forEach(function(sec) {
                    const top = sec.offsetTop - 120;
                    if (scrollTop >= top) {
                        current = sec.getAttribute('id');
                    }
                });

                navLinks.forEach(function(link) {
                    link.classList.remove('active-nav');
                    if (link.getAttribute('href') === '#' + current) {
                        link.classList.add('active-nav');
                    }
                });
            }

            window.addEventListener('scroll', updateScroll, { passive: true });
            updateScroll();

            backToTop.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            const revealEls = document.querySelectorAll('.reveal');
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            revealEls.forEach(function(el) {
                observer.observe(el);
            });

            const counters = document.querySelectorAll('[data-count]');
            const counterObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const target = parseFloat(el.getAttribute('data-count'));
                        const suffix = el.getAttribute('data-suffix') || '';
                        const decimals = parseInt(el.getAttribute('data-decimal') || '0', 10);
                        const duration = 1600;
                        const start = performance.now();

                        function tick(now) {
                            const elapsed = now - start;
                            const progress = Math.min(elapsed / duration, 1);
                            const eased = 1 - Math.pow(1 - progress, 3);
                            const current = (target * eased).toFixed(decimals);
                            el.textContent = current + suffix;
                            if (progress < 1) {
                                requestAnimationFrame(tick);
                            } else {
                                el.textContent = target.toFixed(decimals) + suffix;
                            }
                        }

                        requestAnimationFrame(tick);
                        counterObserver.unobserve(el);
                    }
                });
            }, { threshold: 0.5 });

            counters.forEach(function(el) {
                counterObserver.observe(el);
            });
        })();
    </script>
</body>

</html>