<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - idSpora</title>
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
      }

      .main-container {
        background: #ffffff;
        margin: 0;
        overflow: hidden;
      }

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

      .btn-contact {
        background: var(--dark-blue);
        color: white;
        border-radius: 25px;
        padding: 10px 25px;
        border: none;
        text-decoration: none;
        transition: all 0.3s ease;
      }

      .btn-contact:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      }

      .btn-contact.active {
        background: var(--primary-yellow);
        color: var(--dark-blue);
      }

      .page-header {
        padding: 100px 0 60px;
        text-align: center;
      }

      .page-title {
        font-size: 3rem;
        font-weight: bold;
        color: var(--dark-blue);
        margin-bottom: 20px;
      }

      .page-subtitle {
        font-size: 1.2rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
      }

      .highlight-yellow {
        background-color: var(--primary-yellow);
        padding: 5px 15px;
        font-weight: bold;
        border-radius: 10px;
        display: inline-block;
      }

      .contact-form-card,
      .contact-info-card {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        height: 100%;
        border: 1px solid rgba(244, 196, 48, 0.1);
      }

      .contact-form .form-control {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: border-color 0.3s ease;
      }

      .contact-form .form-control:focus {
        border-color: var(--primary-yellow);
        box-shadow: 0 0 0 0.2rem rgba(244, 196, 48, 0.25);
      }

      .contact-item {
        padding: 15px 0;
        border-bottom: 1px solid rgba(244, 196, 48, 0.1);
      }

      .contact-item:last-child {
        border-bottom: none;
      }

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

      .section-spacing {
        padding: var(--section-spacing) 0;
      }

      .footer-section {
        background: var(--dark-blue) !important;
        color: white;
        padding: 30px 0 15px;
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

      .footer-section a {
        text-decoration: none !important;
      }

      .footer-section a:hover {
        text-decoration: none !important;
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

      .whatsapp-float i {
        margin-top: 4px;
      }

      @media (max-width: 768px) {
        .page-title {
          font-size: 2rem;
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
              src="property/logo idspora_nobg_outlined.png"
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
                <a class="nav-link" href="index.php">Beranda</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php">Tentang</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="products.php">Portofolio</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="reviews.php">Ulasan</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="news.php">Berita</a>
              </li>
            </ul>
            <a href="contact.php" class="btn btn-contact active"
              >Hubungi Kami</a
            >
          </div>
        </div>
      </nav>

      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb">
        <div class="container">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
            <li class="breadcrumb-item active">Hubungi Kami</li>
          </ol>
        </div>
      </nav>

      <!-- Page Header -->
      <section class="page-header">
        <div class="container">
          <h1 class="page-title" data-aos="fade-up">
            Butuh Bantuan <span class="highlight-yellow">idSpora?</span>
          </h1>
          <p class="page-subtitle" data-aos="fade-up" data-aos-delay="200">
            Hubungi kami untuk konsultasi gratis atau informasi lebih lanjut
            tentang program pembelajaran kami.
          </p>
        </div>
      </section>

      <!-- Contact Section -->
      <section class="section-spacing">
        <div class="container">
          <div class="row">
            <div class="col-lg-8 mb-4" data-aos="fade-right">
              <div class="contact-form-card">
                <h3 class="mb-4">Kirim Pesan</h3>
                <form class="contact-form" id="whatsappForm">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="firstName" class="form-label"
                        >Nama Depan *</label
                      >
                      <input
                        type="text"
                        class="form-control"
                        id="firstName"
                        required
                      />
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="lastName" class="form-label"
                        >Nama Belakang *</label
                      >
                      <input
                        type="text"
                        class="form-control"
                        id="lastName"
                        required
                      />
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="email" class="form-label">Email *</label>
                      <input
                        type="email"
                        class="form-control"
                        id="email"
                        required
                      />
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="phone" class="form-label">No. Telepon</label>
                      <input type="tel" class="form-control" id="phone" />
                    </div>
                    <div class="col-12 mb-3">
                      <label for="subject" class="form-label">Subjek *</label>
                      <select class="form-control" id="subject" required>
                        <option value="">Pilih subjek</option>
                        <option value="webinar">Informasi Webinar</option>
                        <option value="training">Program Training</option>
                        <option value="workshop">Mini Workshop</option>
                        <option value="elearning">E-Learning Course</option>
                        <option value="partnership">Kemitraan</option>
                        <option value="other">Lainnya</option>
                      </select>
                    </div>
                    <div class="col-12 mb-3">
                      <label for="message" class="form-label">Pesan *</label>
                      <textarea
                        class="form-control"
                        id="message"
                        rows="5"
                        required
                        placeholder="Ceritakan kebutuhan pembelajaran Anda..."
                      ></textarea>
                    </div>
                    <div class="col-12">
                      <button type="submit" class="btn btn-success btn-lg">
                        <i class="fab fa-whatsapp me-2"></i>Kirim ke WhatsApp
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <div class="col-lg-4 mb-4" data-aos="fade-left">
              <div class="contact-info-card">
                <h3 class="mb-4">Informasi Kontak</h3>

                <div class="contact-item">
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <i
                        class="fas fa-map-marker-alt fa-lg"
                        style="color: var(--primary-yellow)"
                      ></i>
                    </div>
                    <div>
                      <h6>Alamat</h6>
                      <p class="text-muted mb-0">
                        Gd. Selaru Lt.4 Telkom University<br />Jl.
                        Telekomunikasi No.1 Bandung<br />Jawa Barat, 40267
                      </p>
                    </div>
                  </div>
                </div>

                <div class="contact-item">
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <i
                        class="fas fa-phone fa-lg"
                        style="color: var(--primary-yellow)"
                      ></i>
                    </div>
                    <div>
                      <h6>Telepon</h6>
                      <p class="text-muted mb-0">+62 898-9260-731</p>
                    </div>
                  </div>
                </div>

                <div class="contact-item">
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <i
                        class="fas fa-envelope fa-lg"
                        style="color: var(--primary-yellow)"
                      ></i>
                    </div>
                    <div>
                      <h6>Email</h6>
                      <p class="text-muted mb-0">idspora.contact@gmail.com</p>
                    </div>
                  </div>
                </div>

                <div class="contact-item">
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <i
                        class="fas fa-clock fa-lg"
                        style="color: var(--primary-yellow)"
                      ></i>
                    </div>
                    <div>
                      <h6>Jam Operasional</h6>
                      <p class="text-muted mb-0">
                        Senin - Jumat: 09:00 - 17:00<br />
                      </p>
                    </div>
                  </div>
                </div>

                <div class="contact-item">
                  <h6>Ikuti Media Sosial idSpora</h6>
                  <div class="social-links">
                    <a
                      href="https://www.tiktok.com/@idspora"
                      class="me-3"
                      style="color: var(--dark-blue)"
                      ><i class="fab fa-tiktok fa-lg"></i
                    ></a>
                    <a
                      href="https://www.instagram.com/idspora.official/"
                      class="me-3"
                      style="color: var(--dark-blue)"
                      ><i class="fab fa-instagram fa-lg"></i
                    ></a>
                    <a
                      href="https://www.linkedin.com/company/idspora/"
                      style="color: var(--dark-blue)"
                      ><i class="fab fa-linkedin fa-lg"></i
                    ></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Quick Consultation Section -->
      <section class="py-5 bg-light">
        <div class="container text-center">
          <h2 class="section-title mb-4" data-aos="fade-up">
            Butuh Konsultasi Cepat?
          </h2>
          <p class="lead mb-4" data-aos="fade-up" data-aos-delay="200">
            Tim ahli kami siap membantu Anda via WhatsApp untuk konsultasi
            gratis.
          </p>
          <div data-aos="fade-up" data-aos-delay="400">
            <a
              href="https://wa.me/628989260731"
              class="btn btn-success btn-lg me-3"
              target="_blank"
            >
              <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
            </a>
            <a href="tel:+628989260731" class="btn btn-outline-dark btn-lg">
              <i class="fas fa-phone me-2"></i>Telepon Sekarang
            </a>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="footer-section">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-3 col-md-6 mb-4">
              <a class="navbar-brand" href="index.php"
                ><img
                  src="property/logo idspora_nobg_dark.png"
                  alt="idSpora Logo"
                  style="height: 60px; width: auto"
              /></a>
              <p class="text-light">
                Belajar tanpa batas, berkembang tanpa henti.
              </p>
              <div class="social-links">
                <a
                  href="https://www.tiktok.com/@idspora"
                  class="text-light me-3"
                  ><i class="fab fa-tiktok"></i
                ></a>
                <a
                  href="https://www.instagram.com/idspora.official/"
                  class="text-light me-3"
                  ><i class="fab fa-instagram"></i
                ></a>
                <a
                  href="https://www.linkedin.com/company/idspora/"
                  class="text-light"
                  ><i class="fab fa-linkedin"></i
                ></a>
              </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
              <h6 class="text-white mb-3">Quick Links</h6>
              <ul class="list-unstyled">
                <li><a href="index.php" class="text-light">Beranda</a></li>
                <li>
                  <a href="about.php" class="text-light">Tentang Kami</a>
                </li>
                <li>
                  <a href="products.php" class="text-light">Portofolio</a>
                </li>
                <li><a href="reviews.php" class="text-light">Ulasan</a></li>
                <li><a href="news.php" class="text-light">Berita</a></li>
              </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <h6 class="text-white mb-3">Layanan</h6>
              <ul class="list-unstyled">
                <li><a href="#" class="text-light">Live Webinars</a></li>
                <li>
                  <a href="#" class="text-light">Training & Mini Workshops</a>
                </li>
                <li><a href="#" class="text-light">E-Learning</a></li>
                <li><a href="#" class="text-light">Video Production</a></li>
              </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <h6 class="text-white mb-3">Hubungi Kami</h6>
              <p class="text-light mb-2">
                <i class="fas fa-envelope me-2"></i>info@idspora.com
              </p>
              <p class="text-light mb-2">
                <i class="fas fa-phone me-2"></i>+62 898-926-0731
              </p>
              <p class="text-light mb-3">
                <i class="fas fa-map-marker-alt me-2"></i>Bandung, Indonesia
              </p>
              <div class="social-links">
                <a
                  href="https://www.tiktok.com/@idspora"
                  class="text-light me-3"
                  title="TikTok"
                  ><i class="fab fa-tiktok"></i
                ></a>
                <a
                  href="https://www.instagram.com/idspora.official/"
                  class="text-light me-3"
                  title="Instagram"
                  ><i class="fab fa-instagram"></i
                ></a>
                <a
                  href="https://www.linkedin.com/company/idspora/"
                  class="text-light"
                  title="LinkedIn"
                  ><i class="fab fa-linkedin"></i
                ></a>
              </div>
            </div>
          </div>
          <hr class="my-3" style="border-color: #495057" />
          <div class="row justify-content-center">
            <div class="col-md-4 text-center">
              <p class="text-light mb-1" style="font-size: 0.8rem">
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

      // WhatsApp form submission handler
      document
        .querySelector("#whatsappForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();

          // Get form data
          const firstName = document.getElementById("firstName").value.trim();
          const lastName = document.getElementById("lastName").value.trim();
          const email = document.getElementById("email").value.trim();
          const phone = document.getElementById("phone").value.trim();
          const subjectSelect = document.getElementById("subject");
          const subjectValue = subjectSelect.value;
          const subjectText = subjectValue
            ? subjectSelect.options[subjectSelect.selectedIndex].text
            : "Belum dipilih";
          const message = document.getElementById("message").value.trim();

          // Validation
          if (!firstName || !lastName || !email || !subjectValue || !message) {
            alert("Mohon lengkapi semua field yang wajib diisi!");
            return;
          }

          // Create simple WhatsApp message
          let whatsappMessage = `Halo idSpora! 👋

Informasi Kontak:
📝 Nama: ${firstName} ${lastName}
📧 Email: ${email}`;

          if (phone) {
            whatsappMessage += `
📞 Telepon: ${phone}`;
          }

          whatsappMessage += `

Subjek: ${subjectText}

Pesan:
${message}

Mohon informasi lebih lanjut. Terima kasih! 🙏`;

          // Debug: Show message in console
          console.log("WhatsApp Message:", whatsappMessage);

          // Encode the message for URL
          const encodedMessage = encodeURIComponent(whatsappMessage);

          // Open WhatsApp
          const whatsappURL = "https://wa.me/628989260731?text=${encodedMessage};"
          console.log("WhatsApp URL:", whatsappURL);

          window.open(whatsappURL, "_blank");

          // Show success message
          const button = this.querySelector('button[type="submit"]');
          const originalText = button.innerHTML;

          button.innerHTML =
            '<i class="fas fa-check me-2"></i>Mengarahkan ke WhatsApp...';
          button.disabled = true;

          setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
            this.reset();
          }, 3000);
        });
    </script>
  </body>
</html>
