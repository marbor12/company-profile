<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
      idSpora Meluncurkan Program AI Bootcamp untuk Profesional Indonesia -
      idSpora
    </title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <style>
      :root {
        --primary-yellow: #f4c430;
        --dark-blue: #2c3e50;
        --light-cream: #fdf6e3;
        --section-spacing: 100px;
      }

      body {
        background: #ffffff;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        line-height: 1.7;
      }

      .main-container {
        background: #ffffff;
        margin: 0;
        overflow: hidden;
      }

      /* Navbar */
      .navbar {
        background: transparent;
        padding: 20px 0;
        border-bottom: 1px solid rgba(244, 196, 48, 0.1);
      }

      .navbar-brand {
        display: flex;
        align-items: center;
        text-decoration: none;
        position: relative;
        z-index: 10;
      }

      .navbar-brand img {
        height: 70px;
        width: auto;
        transition: all 0.3s ease;
        margin: -15px 0;
      }

      .navbar-brand:hover img {
        transform: scale(1.05);
      }

      .navbar-nav .nav-link {
        color: var(--dark-blue) !important;
        font-weight: 500;
        margin: 0 15px;
        transition: all 0.3s ease;
        position: relative;
      }

      .navbar-nav .nav-link::after {
        content: "";
        position: absolute;
        bottom: -5px;
        left: 50%;
        width: 0;
        height: 2px;
        background: var(--primary-yellow);
        transition: all 0.3s ease;
        transform: translateX(-50%);
      }

      .navbar-nav .nav-link:hover::after,
      .navbar-nav .nav-link.active::after {
        width: 100%;
      }

      .btn-contact:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      }

      /* Breadcrumb */
      .breadcrumb {
        background: transparent;
        padding: 20px 0;
      }

      .breadcrumb-item a {
        color: var(--dark-blue);
        text-decoration: none;
      }

      .breadcrumb-item.active {
        color: var(--primary-yellow);
      }

      /* Article Styles */
      .article-header {
        padding: 60px 0 40px;
        background: linear-gradient(
          135deg,
          rgba(244, 196, 48, 0.05),
          rgba(255, 215, 0, 0.02)
        );
        border-bottom: 2px solid rgba(244, 196, 48, 0.1);
      }

      .article-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--dark-blue);
        margin-bottom: 20px;
      }

      .article-meta {
        font-size: 1rem;
        color: #666;
        margin-bottom: 20px;
      }

      .article-content {
        padding: 60px 0;
      }

      .article-body {
        background: transparent;
        padding: 0;
        border-radius: 0;
        box-shadow: none;
        border: none;
      }

      .article-body img {
        border-radius: 15px;
        margin: 30px 0;
      }

      .article-body h2 {
        color: var(--dark-blue);
        margin-top: 40px;
        margin-bottom: 20px;
        font-size: 1.8rem;
        font-weight: 600;
      }

      .article-body h3 {
        color: var(--dark-blue);
        margin-top: 30px;
        margin-bottom: 15px;
        font-size: 1.4rem;
        font-weight: 600;
      }

      .article-body p {
        margin-bottom: 20px;
        font-size: 1.1rem;
      }

      .article-body ul,
      .article-body ol {
        margin-bottom: 20px;
        padding-left: 30px;
      }

      .article-body li {
        margin-bottom: 8px;
        font-size: 1.1rem;
      }

      .highlight-box {
        background: #f8f9fa;
        border-left: 3px solid var(--primary-yellow);
        padding: 15px;
        margin: 25px 0;
        border-radius: 0;
      }

      .highlight-yellow {
        background-color: var(--primary-yellow);
        padding: 3px 8px;
        font-weight: 600;
        border-radius: 3px;
        display: inline;
      }

      /* Sidebar */
      .sidebar {
        background: transparent;
        padding: 0;
        border-radius: 0;
        box-shadow: none;
        border: none;
        position: sticky;
        top: 20px;
      }

      .sidebar-widget {
        padding-bottom: 15px;
        border-bottom: none;
        margin-bottom: 25px;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
      }

      .sidebar-widget:last-child {
        border-bottom: none;
        margin-bottom: 0;
      }

      .sidebar-widget a {
        color: var(--dark-blue);
        transition: color 0.3s ease;
        text-decoration: none;
      }

      .sidebar-widget a:hover {
        color: var(--primary-yellow);
      }

      /* Related Articles */
      .related-articles {
        padding: 40px 0;
        background: #ffffff;
      }

      .related-card {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
        border: 1px solid rgba(244, 196, 48, 0.1);
        height: 100%;
      }

      .related-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
      }

      .related-card img {
        border-radius: 10px;
        transition: all 0.4s ease;
        width: 100%;
        height: 200px;
        object-fit: cover;
      }

      .related-card:hover img {
        transform: scale(1.05);
      }

      /* Footer */
      .footer-section {
        background: var(--dark-blue) !important;
        color: white;
        padding: 60px 0 40px;
        position: relative;
      }

      .footer-section::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(
          90deg,
          transparent,
          var(--primary-yellow),
          transparent
        );
      }

      /* WhatsApp Floating Button */
      .whatsapp-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #25d366;
        color: white;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 3px #999;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        text-decoration: none;
      }

      .whatsapp-float:hover {
        background-color: #128c7e;
        transform: scale(1.1);
        color: white;
        text-decoration: none;
      }

      /* Responsive */
      @media (max-width: 768px) {
        .article-title {
          font-size: 2rem;
        }

        .article-body {
          padding: 20px;
        }

        .sidebar {
          margin-top: 30px;
        }
      }
    </style>
  </head>
  <body>
    <div class="main-container">
      <!-- Navigation -->
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <a class="navbar-brand" href="index.php"
            ><img
              src="public/images/logo idspora_nobg_outlined.png"
              alt="idSpora Logo"
          /></a>

          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
          >
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-4">
              <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php">About Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="products.php">Product</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="reviews.php">Review</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="news.php">News</a>
              </li>
            </ul>
            <a href="contact.php" class="btn btn-contact">Contact Us</a>
          </div>
        </div>
      </nav>

      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb">
        <div class="container">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="news.php">News</a></li>
            <li class="breadcrumb-item active">AI Bootcamp Program</li>
          </ol>
        </div>
      </nav>

      <!-- Article Header -->
      <section class="article-header">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-10">
              <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-3">Featured</span>
                <span class="badge bg-success me-3">AI & Technology</span>
              </div>
              <h1 class="article-title">
                idSpora Meluncurkan Program AI Bootcamp untuk Profesional
                Indonesia
              </h1>
              <div class="article-meta">
                <i class="fas fa-calendar-alt me-2"></i>15 Januari 2025
                <span class="mx-3">•</span>
                <i class="fas fa-clock me-2"></i>8 min read
                <span class="mx-3">•</span>
                <i class="fas fa-user me-2"></i>Tim idSpora
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Article Content -->
      <section class="article-content">
        <div class="container">
          <div class="row">
            <div class="col-lg-8">
              <div data-aos="fade-up">
                <img
                  src="public/images/hero-training.png"
                  alt="AI Bootcamp Program"
                  class="img-fluid mb-4"
                  style="border-radius: 15px"
                />

                <p class="lead">
                  Kami dengan bangga mengumumkan peluncuran program AI Bootcamp
                  terbaru yang dirancang khusus untuk meningkatkan kompetensi
                  profesional Indonesia di bidang kecerdasan buatan. Program ini
                  menjadi milestone penting dalam misi idSpora untuk menciptakan
                  ekosistem pembelajaran digital yang berkelanjutan.
                </p>

                <h2>Mengapa AI Bootcamp?</h2>
                <p>
                  Perkembangan teknologi AI telah mengubah industri global. Di
                  Indonesia, kebutuhan profesional AI meningkat, namun masih ada
                  gap antara demand dan supply talent yang berkualitas.
                </p>

                <p>
                  Riset menunjukkan 85% perusahaan Indonesia berencana
                  mengadopsi AI dalam 2-3 tahun ke depan, namun hanya 23% yang
                  memiliki tim dengan kompetensi AI memadai.
                </p>

                <div class="highlight-box">
                  <strong>Fakta:</strong> Implementasi AI dapat meningkatkan
                  produktivitas perusahaan hingga 40% (McKinsey Global
                  Institute).
                </div>

                <h2>Program Highlights</h2>

                <h3>Kurikulum Praktis</h3>
                <p>
                  Program mencakup Machine Learning, Deep Learning, NLP, dan
                  Computer Vision dengan pendekatan hands-on dan project-based
                  learning.
                </p>

                <h3>Instruktur Berpengalaman</h3>
                <p>
                  Diampu praktisi AI dari perusahaan seperti Google, Microsoft,
                  dan startup unicorn Indonesia.
                </p>

                <img
                  src="public/images/workshop-session.png"
                  alt="AI Workshop Session"
                  class="img-fluid mb-4"
                  style="border-radius: 15px"
                />

                <h2>Timeline Program</h2>

                <p>
                  Program berlangsung
                  <span class="highlight-yellow">12 minggu</span>
                  dengan format hybrid learning yang fleksibel.
                </p>

                <h2>Target Peserta</h2>

                <p>Program cocok untuk:</p>
                <ul>
                  <li>Software engineers yang ingin transisi ke AI/ML</li>
                  <li>Data analysts yang ingin upgrade skill</li>
                  <li>Product managers di tech companies</li>
                  <li>Fresh graduates background STEM</li>
                </ul>

                <div class="highlight-box">
                  <strong>Syarat:</strong> Basic programming knowledge dan
                  passion untuk belajar teknologi AI.
                </div>

                <h2>Benefit Program</h2>

                <p>Peserta akan mendapatkan:</p>
                <ul>
                  <li>Sertifikat resmi idSpora AI Bootcamp</li>
                  <li>Portfolio proyek AI siap showcase</li>
                  <li>Akses komunitas alumni</li>
                  <li>Career coaching dan job placement</li>
                </ul>

                <h2>Pendaftaran</h2>

                <p>
                  Batch pertama dimulai <strong>1 Maret 2025</strong> dengan
                  kuota 30 peserta. Early bird discount 25% untuk 10 pendaftar
                  pertama.
                </p>

                <div class="text-center mt-5">
                  <a
                    href="https://wa.me/628989260731?text=Halo%20idSpora,%20saya%20tertarik%20dengan%20program%20AI%20Bootcamp"
                    class="btn btn-dark btn-lg me-3"
                    target="_blank"
                  >
                    <i class="fab fa-whatsapp me-2"></i>Daftar Sekarang
                  </a>
                  <a href="products.php" class="btn btn-outline-dark btn-lg">
                    Lihat Program Lainnya
                  </a>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <!-- Sidebar -->
              <div class="sidebar">
                <div class="sidebar-widget">
                  <h6 class="mb-3">Quick Info</h6>
                  <ul class="list-unstyled small">
                    <li class="mb-1"><strong>Duration:</strong> 12 weeks</li>
                    <li class="mb-1">
                      <strong>Format:</strong> Online + Offline
                    </li>
                    <li class="mb-1"><strong>Max:</strong> 30 participants</li>
                    <li class="mb-1"><strong>Start:</strong> 1 Maret 2025</li>
                  </ul>
                </div>

                <div class="sidebar-widget">
                  <h6 class="mb-3">Contact</h6>
                  <p class="mb-2 small">
                    <i class="fas fa-envelope me-2"></i>
                    idspora.contact@gmail.com
                  </p>
                  <p class="mb-0 small">
                    <i class="fab fa-whatsapp me-2"></i>
                    +62 899-8926-0731
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Related Articles -->
      <section class="related-articles">
        <div class="container">
          <h3 class="text-center mb-4">
            Artikel <span class="highlight-yellow">Terkait</span>
          </h3>
          <div class="row">
            <div
              class="col-lg-4 col-md-6 mb-4"
              data-aos="fade-up"
              data-aos-delay="100"
            >
              <div class="related-card">
                <img
                  src="public/images/workshop-session.png"
                  alt="AI Trends"
                  class="img-fluid mb-3"
                />
                <span class="badge bg-success mb-2">AI & Technology</span>
                <h5 class="mb-3">
                  5 Tren AI yang Akan Mengubah Industri di 2025
                </h5>
                <p class="text-muted mb-3">
                  Pelajari tren kecerdasan buatan terbaru yang akan membentuk
                  masa depan berbagai industri.
                </p>
                <a
                  href="article-ai-trends.html"
                  class="btn btn-outline-dark btn-sm"
                  >Read More</a
                >
              </div>
            </div>

            <div
              class="col-lg-4 col-md-6 mb-4"
              data-aos="fade-up"
              data-aos-delay="200"
            >
              <div class="related-card">
                <img
                  src="public/images/training-roi.png"
                  alt="Data Science Career"
                  class="img-fluid mb-3"
                />
                <span class="badge bg-info mb-2">Career</span>
                <h5 class="mb-3">
                  Cara Memulai Karir di Data Science dari Nol
                </h5>
                <p class="text-muted mb-3">
                  Panduan lengkap untuk memulai karir di bidang data science
                  yang sedang booming.
                </p>
                <a
                  href="article-data-science-career.php"
                  class="btn btn-outline-dark btn-sm"
                  >Read More</a
                >
              </div>
            </div>

            <div
              class="col-lg-4 col-md-6 mb-4"
              data-aos="fade-up"
              data-aos-delay="300"
            >
              <div class="related-card">
                <img
                  src="public/images/consultation.png"
                  alt="Online Learning"
                  class="img-fluid mb-3"
                />
                <span class="badge bg-warning mb-2">Learning Tips</span>
                <h5 class="mb-3">
                  Tips Sukses Mengikuti Webinar dan Training Online
                </h5>
                <p class="text-muted mb-3">
                  Strategi efektif untuk memaksimalkan pembelajaran online dan
                  mendapatkan hasil terbaik.
                </p>
                <a
                  href="article-online-learning-tips.html"
                  class="btn btn-outline-dark btn-sm"
                  >Read More</a
                >
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="footer-section">
        <div class="container">
          <div class="row">
            <div class="col-lg-4 col-md-4 mb-4">
              <a class="navbar-brand" href="index.php"
                ><img
                  src="public/images/logo idspora_nobg_dark.png"
                  alt="idSpora Logo"
              /></a>
              <p class="text-light">
                Empowering Lifelong Learning Through Innovation and
                Collaboration.
              </p>
              <div class="social-links">
                <a href="#" class="text-light me-3"
                  ><i class="fab fa-tiktok"></i
                ></a>
                <a href="#" class="text-light me-3"
                  ><i class="fab fa-instagram"></i
                ></a>
                <a href="#" class="text-light"
                  ><i class="fab fa-linkedin"></i
                ></a>
              </div>
            </div>
            <div class="col-lg-4 col-md-4 mb-4 text-center">
              <h6 class="text-white mb-3">Quick Links</h6>
              <ul class="list-unstyled">
                <li>
                  <a
                    href="index.php"
                    class="text-light"
                    style="text-decoration: none"
                    >Home</a
                  >
                </li>
                <li>
                  <a
                    href="products.php"
                    class="text-light"
                    style="text-decoration: none"
                    >Portofolio</a
                  >
                </li>
                <li>
                  <a
                    href="about.php"
                    class="text-light"
                    style="text-decoration: none"
                    >About Us</a
                  >
                </li>
                <li>
                  <a
                    href="reviews.php"
                    class="text-light"
                    style="text-decoration: none"
                    >Review</a
                  >
                </li>
                <li>
                  <a
                    href="news.php"
                    class="text-light"
                    style="text-decoration: none"
                    >News</a
                  >
                </li>
              </ul>
            </div>
            <div class="col-lg-4 col-md-4 mb-4">
              <h6 class="text-white mb-3">Newsletter</h6>
              <p class="text-light">
                Subscribe to get updates on latest training and webinar
                schedules.
              </p>
              <div class="input-group">
                <input
                  type="email"
                  class="form-control"
                  placeholder="Your email"
                />
                <button
                  class="btn"
                  style="
                    background: var(--primary-yellow);
                    color: var(--dark-blue);
                  "
                >
                  Subscribe
                </button>
              </div>
            </div>
          </div>
          <hr class="mt-2 mb-2" style="border-color: #495057" />
          <div class="row justify-content-center">
            <div class="col-md-4 text-center">
              <p class="text-light mb-2" style="font-size: 0.9rem">
                &copy; 2024 idSpora. All rights reserved.
              </p>
            </div>
          </div>
        </div>
      </footer>
    </div>

    <!-- WhatsApp Floating Button -->
    <a
      href="https://wa.me/628989260731"
      class="whatsapp-float"
      target="_blank"
      title="Chat with us on WhatsApp"
    >
      <i class="fab fa-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({
        duration: 800,
        easing: "ease-in-out",
        once: true,
      });
    </script>
  </body>
</html>
