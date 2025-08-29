<?php
require_once 'Admin/config/database.php';

$pdo = getDBConnection();
$berita_per_halaman = 8;

$total_berita_stmt = $pdo->query("SELECT COUNT(*) FROM news");
$total_berita = $total_berita_stmt->fetchColumn();

$total_halaman = ceil($total_berita / $berita_per_halaman);

$halaman_sekarang = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($halaman_sekarang > $total_halaman) {
    $halaman_sekarang = $total_halaman;
}
if ($halaman_sekarang < 1) {
    $halaman_sekarang = 1;
}

$offset = ($halaman_sekarang - 1) * $berita_per_halaman;

$stmt = $pdo->prepare("SELECT * FROM news ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $berita_per_halaman, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftar_berita = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set active page for header
$activePage = 'news';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>News - idSpora</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
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

    .btn-contact {
      background: var(--dark-blue);
      color: white;
      border-radius: 25px;
      padding: 10px 25px;
      border: none;
      transition: all 0.3s ease;
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

    /* Page Header */
    .page-header {
      padding: 60px 0;
      background: linear-gradient(135deg,
          rgba(244, 196, 48, 0.05),
          rgba(255, 215, 0, 0.02));
      text-align: center;
      border-bottom: 2px solid rgba(244, 196, 48, 0.1);
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

    /* Sections */
    .section-spacing-sm {
      padding: 60px 0;
    }

    .section-title {
      font-size: 2.5rem;
      font-weight: bold;
      color: var(--dark-blue);
    }

    /* Featured Article */
    .featured-article {
      background: white;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      border: 2px solid rgba(244, 196, 48, 0.1);
    }

    .featured-article img {
      border-radius: 15px;
    }

    /* Sidebar */
    .blog-sidebar {
      background: white;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(244, 196, 48, 0.1);
    }

    .sidebar-widget {
      padding-bottom: 20px;
      border-bottom: 1px solid #f0f0f0;
    }

    .sidebar-widget:last-child {
      border-bottom: none;
    }

    .sidebar-widget a {
      color: var(--dark-blue);
      transition: color 0.3s ease;
    }

    .sidebar-widget a:hover {
      color: var(--primary-yellow);
    }

    /* News Cards */
    .news-card {
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: all 0.4s ease;
      border: 1px solid rgba(244, 196, 48, 0.1);
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .news-card .card-content {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .news-card .card-text {
      flex: 1;
      overflow: hidden;
    }

    .news-card .card-footer {
      margin-top: auto;
      padding-top: 15px;
    }

    .news-card h5 {
      font-size: 1.1rem;
      line-height: 1.4;
      min-height: 2.8rem;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .news-card .card-text p {
      font-size: 0.9rem;
      line-height: 1.5;
      min-height: 4.5rem;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .news-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .news-card img {
      border: 1px solid #e9ecef;
      border-radius: 10px;
      transition: all 0.4s ease;
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .news-card:hover img {
      transform: scale(1.05);
    }

    .news-card .badge {
      font-size: 0.8rem;
    }

    /* Pagination */
    .pagination .page-link {
      color: var(--dark-blue);
      border-color: rgba(244, 196, 48, 0.3);
    }

    .pagination .page-item.active .page-link {
      background-color: var(--primary-yellow);
      border-color: var(--primary-yellow);
      color: var(--dark-blue);
    }

    .pagination .page-link:hover {
      background-color: rgba(244, 196, 48, 0.1);
      border-color: var(--primary-yellow);
      color: var(--dark-blue);
    }

    /* Footer */
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
      background: linear-gradient(90deg,
          transparent,
          var(--primary-yellow),
          transparent);
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

    /* Responsive */
    @media (max-width: 768px) {
      .page-title {
        font-size: 2rem;
      }

      .featured-article {
        padding: 20px;
      }

      .blog-sidebar {
        margin-top: 30px;
      }

      .news-card h5 {
        font-size: 1rem;
        min-height: 2.4rem;
      }

      .news-card .card-text p {
        font-size: 0.85rem;
        min-height: 4rem;
      }
    }
  </style>
</head>

<body>
  <div class="main-container">
    <!-- Include Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
      <div class="container">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
          <li class="breadcrumb-item active">Berita</li>
        </ol>
      </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
      <div class="container">
        <h1 class="page-title">
          <span class="highlight-yellow">Info </span> Terbaru
        </h1>
        <p class="page-subtitle">
          Dapatkan info terbaru seputar teknologi dan pembelajaran digital
          dari idSpora.
        </p>
      </div>
    </section>

    <!-- Blog Articles Grid -->
    <section class="section-spacing-sm" style="background: #ffffff">
      <div class="container">
        <div class="row">
          <?php if (!empty($daftar_berita)): ?>
            <?php foreach ($daftar_berita as $article): ?>
              <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="news-card">
                  <?php
                    $image_src = htmlspecialchars($article['picture']);
                    if (strpos($image_src, 'uploads/') !== 0) {
                      $image_src = 'uploads/' . $image_src;
                    }
                  ?>
                  <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="img-fluid mb-3" />
                  
                  <div class="card-content">
                    <div class="d-flex align-items-center mb-2">
                      <span class="badge bg-primary me-2">Berita</span>
                      <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?php echo formatTanggalIndonesia($article['created_at']); ?>
                      </small>
                    </div>
                    
                    <h5 class="mb-3">
                      <?php echo htmlspecialchars($article['title']); ?>
                    </h5>
                    
                    <div class="card-text">
                      <p class="text-muted mb-3">
                        <?php echo htmlspecialchars(truncateText($article['article'], 150)); ?>
                      </p>
                    </div>
                    
                    <div class="card-footer">
                      <a href="detail_berita.php?id=<?php echo $article['id']; ?>" class="btn btn-outline-dark btn-sm">Lihat Detail</a>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center">
              <p>Tidak ada berita untuk ditampilkan di halaman ini.</p>
            </div>
          <?php endif; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Blog pagination" class="mt-5">
          <ul class="pagination justify-content-center">
            <li class="page-item <?php if($halaman_sekarang <= 1){ echo 'disabled'; } ?>">
              <a class="page-link" href="news.php?page=<?php echo $halaman_sekarang - 1; ?>">Sebelumnya</a>
            </li>

            <?php for($i = 1; $i <= $total_halaman; $i++): ?>
              <li class="page-item <?php if($halaman_sekarang == $i) {echo 'active'; } ?>">
                <a class="page-link" href="news.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>

            <li class="page-item <?php if($halaman_sekarang >= $total_halaman) { echo 'disabled'; } ?>">
              <a class="page-link" href="news.php?page=<?php echo $halaman_sekarang + 1; ?>">Selanjutnya</a>
            </li>
          </ul>
        </nav>
      </div>
    </section>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>
  </div>

  <!-- WhatsApp Floating Button -->
  <a href="https://wa.me/628989260731" class="whatsapp-float" target="_blank" title="Chat with us on WhatsApp">
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
