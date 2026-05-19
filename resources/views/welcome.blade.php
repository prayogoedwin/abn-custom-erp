<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asiwa Bumi Niaga</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Premium Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Playfair Display for Serifs, Inter for Sans-Serif -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-earth: #2c1b18;       /* Deep Espresso Brown */
            --secondary-amber: #c19a6b;     /* Golden Harvest Amber */
            --accent-green: #385a42;         /* Estate Leaf Green */
            --pepper-gray: #4d4d4d;          /* Peppercorn Charcoal */
            --light-cream: #faf8f5;          /* Light Latte Cream */
            --text-dark: #2d221e;
            --text-muted: #70635f;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: var(--light-cream);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .font-serif {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
        }

        /* Utility Styles */
        .text-earth-primary { color: var(--primary-earth); }
        .text-amber-secondary { color: var(--secondary-amber); }
        .text-estate-green { color: var(--accent-green); }
        .bg-earth-primary { background-color: var(--primary-earth); }
        .bg-amber-secondary { background-color: var(--secondary-amber); }
        .bg-estate-green { background-color: var(--accent-green); }
        .bg-cream { background-color: var(--light-cream); }
        .border-amber { border-color: var(--secondary-amber) !important; }

        /* Navigation Customization */
        .navbar {
            transition: all 0.3s ease;
            background-color: rgba(44, 27, 24, 0.96) !important;
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--secondary-amber);
        }
        .navbar-brand {
            font-size: 1.4rem;
        }
        .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--secondary-amber) !important;
        }

        /* Hero Banner Settings */
        .hero-section {
            background: linear-gradient(rgba(44, 27, 24, 0.70), rgba(30, 25, 20, 0.85)), 
                        url('https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&q=80&w=1800') no-repeat center center;
            background-size: cover;
            min-height: 85vh;
            display: flex;
            align-items: center;
            color: #ffffff;
        }

        /* Premium Buttons */
        .btn-harvest {
            background-color: var(--secondary-amber);
            color: var(--primary-earth);
            font-weight: 600;
            border: 2px solid var(--secondary-amber);
            padding: 12px 28px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        .btn-harvest:hover {
            background-color: transparent;
            color: var(--secondary-amber);
            border-color: var(--secondary-amber);
        }

        .btn-outline-harvest {
            background-color: transparent;
            color: #ffffff;
            font-weight: 600;
            border: 2px solid #ffffff;
            padding: 12px 28px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        .btn-outline-harvest:hover {
            background-color: #ffffff;
            color: var(--primary-earth);
        }

        /* Section Cards */
        .stat-card {
            background: #ffffff;
            border-top: 4px solid var(--secondary-amber);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }

        /* Commodity Feature Showcase */
        .commodity-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.06);
            background: #ffffff;
            transition: all 0.3s ease;
        }
        .commodity-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 45px rgba(0,0,0,0.12);
        }

        .img-zoom-container {
            overflow: hidden;
            height: 250px;
        }
        .img-zoom {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .commodity-card:hover .img-zoom {
            transform: scale(1.1);
        }

        /* Interactive B2B Sourcing Calculator */
        .calc-container {
            background-color: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(44, 27, 24, 0.08);
            border: 1px solid rgba(193, 154, 107, 0.25);
        }

        /* Process Section Nodes */
        .process-flow-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 25px;
            border-left: 4px solid var(--accent-green);
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            height: 100%;
        }

        .process-badge {
            width: 45px;
            height: 45px;
            background-color: var(--light-cream);
            border: 2px solid var(--accent-green);
            color: var(--accent-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Floating Custom Notification / Toast */
        .custom-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1050;
            background: #2c1b18;
            color: #fff;
            border-left: 5px solid var(--secondary-amber);
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            max-width: 380px;
            transition: all 0.5s ease;
        }

        /* Custom Accordion Theme */
        .accordion-button:not(.collapsed) {
            background-color: var(--light-cream);
            color: var(--primary-earth);
        }
    </style>
</head>
<body>

    <!-- NAVIGATION BAR -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#hero">
                <img src="{{asset('logo.png')}}" alt="logo" style="max-width: 100px;">
                <div class="d-flex flex-column">
                    <span class="font-serif fw-bold text-white tracking-wide mb-0 lh-1" style="letter-spacing: 1px;">Asiwa Bumi Niaga</span>
                    <span class="text-white-50 uppercase fs-6 fw-light tracking-widest mt-1" style="font-size: 0.65rem; letter-spacing: 2px;"></span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item px-2"><a class="nav-link" href="#tentang">Korporasi</a></li>
                    <li class="nav-item px-2"><a class="nav-link" href="#lokasi">Lokasi</a></li>
                    <li class="nav-item px-2"><a class="nav-link" href="#kontak">Kontak</a></li>
                    <li class="nav-item px-2"><a class="nav-link" href="#faq">FAQ</a></li>
                    
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="tentang" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 text-center text-lg-start">
                    <span class="badge bg-estate-green text-white px-3 py-2 rounded-pill fw-bold mb-3 tracking-wider text-uppercase">
                        <i class="fa-solid fa-earth-asia me-1"></i> Global Sourcing Partner for Coffee & Pepper
                    </span>
                    <h1 class="display-3 mb-4 text-white">Jual Beli Kopi & Lada Unggulan Nusantara</h1>
                    <p class="lead text-white-50 mb-5">Asiwa Bumi Niaga mengelola perkebunan kopi berkelanjutan dan sentra pengolahan lada putih & hitam berkualitas ekspor. Kami menjamin kepastian pasokan berskala industri untuk pasar domestik dan mitra korporat internasional.</p>
                    
                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                        <a href="#lokasi" class="btn btn-harvest px-4 py-3"><i class="fa-solid fa-cubes me-2"></i>Jelajahi Hasil Bumi</a>
                        
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <!-- Right Decorative Asset -->
                    <div class="p-4 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 shadow-lg text-center backdrop-blur">
                        <h4 class="text-white font-serif mb-3">Sertifikasi & Standar Ekspor</h4>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 rounded bg-white bg-opacity-10">
                                    <i class="fa-solid fa-certificate text-amber-secondary fs-3 mb-2"></i>
                                    <p class="small text-white mb-0">Organic Certified</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-white bg-opacity-10">
                                    <i class="fa-solid fa-leaf text-success fs-3 mb-2"></i>
                                    <p class="small text-white mb-0">Fair Trade Eco</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-white bg-opacity-10">
                                    <i class="fa-solid fa-shield-halved text-info fs-3 mb-2"></i>
                                    <p class="small text-white mb-0">HACCP & Halal</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-white bg-opacity-10">
                                    <i class="fa-solid fa-ship text-warning fs-3 mb-2"></i>
                                    <p class="small text-white mb-0">Global Shipping</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ALAMAT & PETA LOKASI KANTOR (Menggantikan Stats Tracker) -->
    <section id="lokasi" class="py-5 bg-white">
        <div class="container mt-n5 position-relative z-3">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="border-top: 5px solid var(--secondary-amber) !important;">
                <div class="row g-0">
                    <!-- Kolor Detail Alamat -->
                    <div class="col-lg-5 bg-earth-primary text-white p-4 p-md-5 d-flex flex-column justify-content-center">
                        <span class="badge bg-estate-green text-white px-3 py-2 rounded-pill fw-bold mb-3 align-self-start text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                            <i class="fa-solid fa-map-location-dot me-1"></i> Kantor Pusat & Pabrik Pengolahan
                        </span>
                        <h3 class="font-serif text-amber-secondary mb-4">Lokasi & Operasional</h3>
                        <p class="text-white-50 mb-4 small">Kami menyambut hangat kunjungan para mitra bisnis, eksportir, dan perwakilan korporat internasional untuk bernegosiasi atau meninjau langsung kualitas komoditas kami.</p>
                        
                        <div class="d-flex align-items-start mb-3">
                            <div class="text-amber-secondary fs-4 me-3 mt-1"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Alamat Resmi</h6>
                                <p class="text-white-50 small mb-0">Sukajadi, Baturaja Timur, Ogan Komering Ulu Regency, South Sumatra 32121</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <div class="text-amber-secondary fs-4 me-3 mt-1"><i class="fa-solid fa-compass"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Titik Koordinat GPS</h6>
                                <p class="text-white-50 small mb-0">-4.104183, 104.161004</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <div class="text-amber-secondary fs-4 me-3 mt-1"><i class="fa-solid fa-calendar-days"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Waktu Layanan</h6>
                                <p class="text-white-50 small mb-0">Senin - Sabtu: 08:00 - 17:00 WIB</p>
                            </div>
                        </div>
                    </div>
                    <!-- Kolom Peta Google Maps Interaktif -->
                    <div class="col-lg-7" style="min-height: 400px; position: relative;">
                        <iframe 
                            src="https://maps.google.com/maps?q=-4.104183,104.161004&z=15&ie=UTF8&iwloc=&output=embed" 
                            width="100%" 
                            height="100%" 
                            style="border: 0; min-height: 400px; display: block;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT & KEMITRAAN FORM -->
    <section id="kontak" class="py-5 bg-cream">
        <div class="container py-4">
            <div class="row g-5">
                <div class="col-lg-12">
                    <span class="text-estate-green fw-bold text-uppercase tracking-wider">Kirim Surat Penawaran</span>
                    <h2 class="text-earth-primary display-5 my-3">Diskusikan Kuota Kontrak Sourcing Komoditas</h2>
                    <p class="text-muted">Mulai diskusi kemitraan dengan mengirimkan permintaan sampel gratis kopi atau lada, ataupun menjadwalkan kunjungan komersial ke kebun penangkaran dan fasilitas pengolahan kami di Lampung, Bangka, dan Malang.</p>
                    
                    <div class="card bg-cream border-0 rounded-4 p-4 mt-4" style="background-color: #f2e2ca;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="text-earth-primary fs-3 me-3"><i class="fa-solid fa-headset"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0">Kontak Kami</h6>
                                <p class="text-muted mb-0">(0735) 326663</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="text-earth-primary fs-3 me-3"><i class="fa-solid fa-envelope-open-text"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0">Surel Resmi B2B</h6>
                                <p class="text-muted mb-0">Asiwabuminiaga@gmail.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
    </section>

    <!-- ALUR SOURCING DAN PROSES KEBUN -->
    <section id="alur-kebun" class="py-5 bg-white border-top border-bottom border-amber">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-estate-green fw-bold text-uppercase tracking-wider">Hulu ke Hilir</span>
                <h2 class="text-earth-primary display-5 mt-2">Prinsip Keamanan Pangan & Pengolahan Mutu</h2>
                <p class="text-muted">Protokol pemrosesan mekanik dan higienitas berlapis yang kami terapkan demi hasil bumi yang bersih.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="process-flow-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="process-badge">01</span>
                            <i class="fa-solid fa-temperature-low fs-3 text-estate-green"></i>
                        </div>
                        <h5 class="fw-bold text-earth-primary">1. Pemanenan Selektif</h5>
                        <p class="text-muted small mb-0">Hanya ceri kopi merah ranum dan buah lada tua berkualitas optimal yang dipetik manual demi mempertahankan integritas rasa dan senyawa aktif.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-flow-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="process-badge">02</span>
                            <i class="fa-solid fa-water fs-3 text-estate-green"></i>
                        </div>
                        <h5 class="fw-bold text-earth-primary">2. Pencucian Modern</h5>
                        <p class="text-muted small mb-0">Proses pencucian dengan de-pulper otomatis untuk memisahkan biji kopi dan butiran lada dari kotoran sisa tanah vulkanik secara instan.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-flow-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="process-badge">03</span>
                            <i class="fa-solid fa-circle-nodes fs-3 text-estate-green"></i>
                        </div>
                        <h5 class="fw-bold text-earth-primary">3. Sortasi Optis (Color-Sorter)</h5>
                        <p class="text-muted small mb-0">Butiran lada hitam/putih dan biji kopi hijau disortir menggunakan sensor optik modern guna menghilangkan biji cacat, kerikil, serta benda asing.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="process-flow-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="process-badge">04</span>
                            <i class="fa-solid fa-box-open fs-3 text-estate-green"></i>
                        </div>
                        <h5 class="fw-bold text-earth-primary">4. Vacuum Bulk Packaging</h5>
                        <p class="text-muted small mb-0">Pengemasan akhir menggunakan kantong anyaman kedap air bervakum internal demi mempertahankan kelembapan rendah selama pelayaran antar-benua.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ACCORDION FAQ -->
    <section id="faq" class="py-5 bg-cream border-top border-bottom">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-estate-green fw-bold text-uppercase tracking-wider">Tanya Jawab Komoditas</span>
                <h2 class="text-earth-primary display-5 mt-2">Pertanyaan Terkait Sourcing & Ekspor</h2>
                <p class="text-muted">Informasi teknis penting seputar kepatuhan standar ekspor kopi dan lada kami.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion shadow-sm rounded-4 overflow-hidden" id="faqAccordion">
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-bold text-earth-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Bagaimana ABN menjamin kemurnian Lada (bebas bahan kimia berbahaya)?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body bg-white text-muted">
                                    Setiap lot lada putih Muntok dan lada hitam Lampung kami melewati uji laboratorium bebas residu pestisida kimiawi sebelum pengemasan. Proses pemutihan lada putih kami dilakukan secara mekanis tradisional alami menggunakan air mengalir murni bebas zat klorin, sehingga menjamin rasa yang sangat autentik dan aman dikonsumsi.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-top">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-earth-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Berapa batas minimum pemesanan (MOQ) untuk pengiriman internasional?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body bg-white text-muted">
                                    Batas minimum pemesanan internasional (MOQ) kami adalah 500 Kg untuk skema pengiriman LCL (Less than Container Load). Untuk FCL (Full Container Load), muatan standar kontainer 20 kaki kami berkisar antara 18 hingga 20 Ton tergantung tipe pengemasan vakum atau karung.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-top">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-earth-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Apakah pembeli dapat meminta spesifikasi kadar air dan defek khusus?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body bg-white text-muted">
                                    Ya. Kami memiliki teknologi pengering mekanik terkontrol yang dapat menyesuaikan kadar kelembapan air (moisture level) sesuai batas impor negara tujuan Anda (misal standar UE maksimal 11.5% untuk biji kopi). Tim QC kami juga dapat menyesuaikan persentase toleransi biji pecah (defect rate) sesuai kebutuhan kontrak komoditas Anda.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    

    <!-- FOOTER -->
    <footer class="bg-earth-primary text-white py-5 border-top border-4 border-amber">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h4 class="font-serif fw-bold text-amber-secondary mb-3"><img src="{{ asset('logo.png') }}" alt="logo" style="max-width: 100px;">Asiwa Bumi Niaga</h4>
                    <p class="text-white-50 small">Memperkokoh kedaulatan hasil bumi Indonesia melalui standarisasi mutu kelas dunia, perdagangan yang berkeadilan, dan ekosistem perkebunan kopi & lada yang modern, berkelanjutan, dan transparan.</p>
                    <div class="d-flex gap-3 fs-5 mt-4">
                        <a href="#" class="text-white hover:text-amber-secondary"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="text-white hover:text-amber-secondary"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="text-white hover:text-amber-secondary"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="text-white hover:text-amber-secondary"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-bold mb-3 text-amber-secondary">Navigasi Bisnis</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#tentang" class="text-white-50 text-decoration-none small">Profil Korporat</a></li>
                        <li class="mb-2"><a href="#hasil-bumi" class="text-white-50 text-decoration-none small">Katalog Hasil Bumi</a></li>
                        <li class="mb-2"><a href="#alur-kebun" class="text-white-50 text-decoration-none small">Sistem Traceability</a></li>
                        <li class="mb-2"><a href="#kalkulator" class="text-white-50 text-decoration-none small">Simulasi Sourcing</a></li>
                        <li class="mb-2"><a href="#kontak" class="text-white-50 text-decoration-none small">Inisiasi Kemitraan</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3 text-amber-secondary">Layanan Tambahan</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><span class="text-white-50 small">Pembersihan Pengayakan Lada</span></li>
                        <li class="mb-2"><span class="text-white-50 small">Private Custom Roast Profile</span></li>
                        <li class="mb-2"><span class="text-white-50 small">White Labeling & Packaging OEM</span></li>
                        <li class="mb-2"><span class="text-white-50 small">Penyediaan Logistik FOB & CIF</span></li>
                    </ul>
                </div>

                
            </div>
            
            <hr class="border-secondary-subtle mt-5 mb-4 opacity-25">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50 small">&copy; 2026 PT Asiwa Bumi Niaga Agro. Hak Cipta Dilindungi Undang-Undang.</p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <a href="#" class="text-white-50 text-decoration-none small me-3">Kebijakan Fitosanitasi</a>
                    <a href="#" class="text-white-50 text-decoration-none small">Syarat & Ketentuan Sourcing</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- INTERACTIVE CLIENT JAVASCRIPT LOGIC -->
    <script>
        // DOM Elements for Calculator
        const calcCommodity = document.getElementById('calcCommodity');
        const calcGrade = document.getElementById('calcGrade');
        const calcVolumeRange = document.getElementById('calcVolumeRange');
        const calcVolumeDisplay = document.getElementById('calcVolumeDisplay');
        const livePerKg = document.getElementById('livePerKg');
        const liveDiscount = document.getElementById('liveDiscount');
        const liveLogistic = document.getElementById('liveLogistic');
        const liveTotalSum = document.getElementById('liveTotalSum');
        const packagingRadios = document.getElementsByName('packRadio');

        // DOM Elements for Filter
        const filterTabs = document.querySelectorAll('.filter-tab');
        const commodityItems = document.querySelectorAll('.commodity-item');

        // Dynamic B2B Commodity Calculation Logic
        function runLiveCalculation() {
            const vol = parseInt(calcVolumeRange.value);
            
            // Format Volume display (Kilogram or Ton conversion)
            if (vol >= 1000) {
                calcVolumeDisplay.innerText = (vol / 1000).toFixed(1) + " Ton";
            } else {
                calcVolumeDisplay.innerText = vol + " Kg";
            }

            // Extract selected base price
            const selectedOpt = calcCommodity.options[calcCommodity.selectedIndex];
            const basePrice = parseInt(selectedOpt.getAttribute('data-price'));

            // Extract grade multiplier
            const selectedGradeOpt = calcGrade.options[calcGrade.selectedIndex];
            const gradeMultiplier = parseFloat(selectedGradeOpt.getAttribute('data-mult'));

            // Packaging Surcharge
            let packMultiplier = 1.0;
            for(let radio of packagingRadios) {
                if (radio.checked && radio.value === "Vacuum") {
                    packMultiplier = 1.05; // 5% vacuum markup
                }
            }

            // Bulk volume discounts
            let discount = 0;
            if (vol >= 20000) {
                discount = 0.12;  // 12% off for 20 tons+ (FCL)
            } else if (vol >= 10000) {
                discount = 0.08;  // 8% off for 10 tons+
            } else if (vol >= 5000) {
                discount = 0.05;  // 5% off for 5 tons+
            } else if (vol >= 2000) {
                discount = 0.02;  // 2% off for 2 tons+
            }

            // Estimate shipment mode & timeline
            let logText = "LCL (Kargo Udara/Darat)";
            if (vol >= 18000) {
                logText = "FCL Kontainer 20' (Kargo Laut)";
            } else if (vol >= 5000) {
                logText = "LCL Kontainer (Kargo Laut)";
            } else if (vol >= 2000) {
                logText = "Kargo Darat / Trucking";
            }

            // Final Calculation
            const pricePerUnit = basePrice * gradeMultiplier * packMultiplier;
            const subtotal = vol * pricePerUnit;
            const grandTotal = subtotal * (1 - discount);

            // Update UI Interface
            livePerKg.innerText = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(pricePerUnit);

            liveDiscount.innerText = (discount * 100) + "% OFF";
            liveLogistic.innerText = logText;

            liveTotalSum.innerText = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(grandTotal);
        }

        // Attach Sourcing Event Listeners
        calcCommodity.addEventListener('change', runLiveCalculation);
        calcGrade.addEventListener('change', runLiveCalculation);
        calcVolumeRange.addEventListener('input', runLiveCalculation);
        packagingRadios.forEach(radio => radio.addEventListener('change', runLiveCalculation));

        // Transfer details from Calculator to Contact RFQ Form
        function quoteTransferToForm() {
            const commodityLabel = calcCommodity.options[calcCommodity.selectedIndex].text;
            const gradeLabel = calcGrade.options[calcGrade.selectedIndex].text;
            const volumeLabel = calcVolumeDisplay.innerText;
            const totalLabel = liveTotalSum.innerText;

            document.getElementById('kategoriKemitraan').value = "Kontrak Suplai Bulanan";
            document.getElementById('pesan').value = `Halo Tim Sourcing ABN,\n\nSaya telah mensimulasikan rencana kebutuhan komoditas kami dengan detail:\n- Komoditas: ${commodityLabel}\n- Grade Sourcing: ${gradeLabel}\n- Volume Bulanan: ${volumeLabel}\n- Estimasi Kontrak Bulanan: ${totalLabel}\n\nMohon siapkan dokumen Request for Proposal (RFP) resmi, surat penawaran harga FOB, dan kirimkan sampel secepatnya.`;
            
            // Smoothly focus/navigate to contact area
            document.getElementById('kontak').scrollIntoView({ behavior: 'smooth' });
        }

        // Catalog Categories Filtering Functionality
        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Change Active Styling of Buttons
                filterTabs.forEach(t => t.classList.remove('active', 'btn-harvest'));
                filterTabs.forEach(t => t.classList.add('btn-outline-dark'));
                
                tab.classList.add('active', 'btn-harvest');
                tab.classList.remove('btn-outline-dark');

                const targetType = tab.getAttribute('data-target');

                commodityItems.forEach(item => {
                    const itemTypes = item.getAttribute('data-type');
                    if (targetType === 'all' || itemTypes.includes(targetType)) {
                        item.classList.remove('d-none');
                    } else {
                        item.classList.add('d-none');
                    }
                });
            });
        });

        // Trigger Calculator selection when user clicks a specific product action
        function autoSelectCommodity(itemName) {
            for (let i = 0; i < calcCommodity.options.length; i++) {
                if (calcCommodity.options[i].text.includes(itemName)) {
                    calcCommodity.selectedIndex = i;
                    break;
                }
            }
            runLiveCalculation();
            document.getElementById('kalkulator').scrollIntoView({ behavior: 'smooth' });
        }

        // Handle contact form simulation with embedded Custom Toast notification
        function handleContactSubmit(event) {
            event.preventDefault();
            
            const clientName = document.getElementById('namaLengkap').value;
            const clientCompany = document.getElementById('namaPerusahaan').value;

            // Set content of Custom Toast
            document.getElementById('toastTitle').innerText = "Permintaan B2B Direkam!";
            document.getElementById('toastBody').innerText = `Terima kasih Bapak/Ibu ${clientName} dari ${clientCompany}. Formulir pengajuan penawaran resmi dan permintaan sampel komoditas Anda telah diteruskan ke Manajer Ekspor kami.`;

            // Reset Contact Form
            document.getElementById('contactForm').reset();

            // Display custom toast and hide after 10 seconds
            const toast = document.getElementById('customToast');
            toast.classList.remove('d-none');
            toast.scrollIntoView({ behavior: 'smooth', block: 'center' });

            setTimeout(() => {
                hideToast();
            }, 10000);
        }

        // Handle newsletter feedback with Custom Toast
        function showNewsletterResponse() {
            document.getElementById('toastTitle').innerText = "Langganan Berhasil!";
            document.getElementById('toastBody').innerText = "Alamat surel Anda berhasil didaftarkan. Anda akan segera menerima Laporan Panen Tahunan & Update Harga Komoditas Internasional perdana pada awal bulan depan.";
            
            const toast = document.getElementById('customToast');
            toast.classList.remove('d-none');
            toast.scrollIntoView({ behavior: 'smooth', block: 'center' });

            setTimeout(() => {
                hideToast();
            }, 8000);
        }

        function hideToast() {
            document.getElementById('customToast').classList.add('d-none');
        }

        // Initial launch of parameters
        window.onload = function() {
            runLiveCalculation();
        }
    </script>
</body>
</html>