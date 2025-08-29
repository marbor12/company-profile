<?php
require_once 'Admin/config/database.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: news.php');
    exit();
}

$id = $_GET['id'];
$pdo = getDBConnection();

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: news.php');
    exit();
}

// Set active page for header
$activePage = 'news';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - idSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        :root {
            --primary-yellow: #f4c430;
            --dark-blue: #2c3e50;
            --light-cream: #fdf6e3;
        }

        /* Override body background for detail page - background putih */
        body {
            background: #ffffff !important;
            font-family: 'Poppins', sans-serif;
        }

        /* Prevent image overflow and zoom issues */
        img {
            max-width: 100%;
            height: auto;
            object-fit: cover;
        }

        .hero-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        /* Main container untuk detail page */
        .main-container {
            background: var(--light-cream);
            border-radius: 20px 20px 0 0;
            margin: 20px 20px 0 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Container untuk hero header */
        .hero-container {
            max-width: 100%;
            overflow: hidden;
            position: relative;
        }

        .hero-header {
            height: 80vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            animation: kenBurns 20s infinite;
            overflow: hidden;
            max-width: 100%;
        }

        .hero-header::after {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-content { 
            z-index: 2; 
            text-align: center; 
            padding: 20px; 
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            font-weight: 700;
            line-height: 1.2;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.7);
            animation: slideUp 1s ease-out 0.5s forwards;
            opacity: 0;
        }

        .hero-date {
            font-size: 1rem;
            animation: slideUp 1s ease-out 0.8s forwards;
            opacity: 0;
        }

        .article-content {
            background-color: #ffffff;
            padding: 80px 0;
            margin-top: -100px;
            border-radius: 50px 50px 0 0;
            position: relative;
            z-index: 3;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.1);
            animation: fadeIn 1.5s ease-out;
        }

        .article-body {
            max-width: 750px;
            margin: 0 auto;
            font-size: 1.15rem;
            line-height: 2;
            color: #343a40;
        }

        .back-button {
            display: inline-block;
            margin-top: 50px;
            background-color: var(--primary-yellow);
            color: var(--dark-blue);
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .back-button:hover {
            background-color: var(--dark-blue);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Breadcrumb */
        .breadcrumb {
            background: transparent;
            padding: 20px 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: var(--dark-blue);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--primary-yellow);
        }

        /* Override navbar for detail page - sama seperti index.php */
        .navbar {
            background: transparent !important;
            padding: 20px 0 !important;
            border-bottom: 1px solid rgba(244, 196, 48, 0.1) !important;
        }

        .navbar-brand {
            display: flex !important;
            align-items: center !important;
            text-decoration: none !important;
            position: relative !important;
            z-index: 10 !important;
        }

        .navbar-brand img {
            height: 70px !important;
            width: auto !important;
            transition: all 0.3s ease !important;
            margin: -15px 0 !important;
        }

        .navbar-brand:hover img {
            transform: scale(1.05) !important;
        }

        .navbar-nav .nav-link {
            color: var(--dark-blue) !important;
            font-weight: 500 !important;
            margin: 0 15px !important;
            transition: all 0.3s ease !important;
            position: relative !important;
        }

        .navbar-nav .nav-link::after {
            content: "" !important;
            position: absolute !important;
            bottom: -5px !important;
            left: 50% !important;
            width: 0 !important;
            height: 2px !important;
            background: var(--primary-yellow) !important;
            transition: all 0.3s ease !important;
            transform: translateX(-50%) !important;
        }

        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            width: 100% !important;
        }

        .btn-contact {
            background: var(--dark-blue) !important;
            color: white !important;
            border-radius: 25px !important;
            padding: 10px 25px !important;
            border: none !important;
            transition: all 0.3s ease !important;
        }

        .btn-contact:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
        }

        @keyframes kenBurns {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .hero-title { 
                font-size: 2.5rem; 
            }
            .article-content { 
                margin-top: -50px; 
                border-radius: 30px 30px 0 0; 
                padding: 50px 0; 
            }
            .hero-header {
                height: 60vh;
                background-size: cover;
                background-position: center;
            }
        }

        /* Responsive image control */
        @media (max-width: 576px) {
            .hero-header {
                height: 50vh;
                background-size: cover;
                background-position: center;
            }
        }

        /* Prevent image overflow on zoom */
        @media screen and (max-resolution: 1dppx) {
            .hero-header {
                background-size: contain;
                background-position: center;
            }
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

        /* Override footer for detail page */
        .footer-section {
            background: linear-gradient(135deg, var(--dark-blue) 0%, #34495e 100%) !important;
            color: white;
            padding: 60px 0 30px;
        }

        .footer-section h6 {
            color: white !important;
            font-weight: 600;
        }

        .footer-section .text-light {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .footer-section .text-light:hover {
            color: white !important;
        }
    </style>
</head>
<body>

    <!-- Include Header -->
    <?php include 'includes/header.php'; ?>

    <div class="main-container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="news.php">Berita</a></li>
                    <li class="breadcrumb-item active">Detail Berita</li>
                </ol>
            </div>
        </nav>

    <?php
        $image_src = htmlspecialchars($article['picture']);
        
        if (strpos($image_src, 'uploads/') !== 0) {
            $image_src = 'uploads/' . $image_src;
        }
    ?>

    <div class="hero-container">
        <header class="hero-header" style="background-image: url('<?php echo $image_src; ?>');">
            <div class="hero-content">
                <h1 class="hero-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                <p class="hero-date">
                    <i class="fas fa-calendar-alt"></i> 
                    Diterbitkan pada <?php echo formatTanggalIndonesia($article['created_at']); ?>
                </p>
            </div>
        </header>
    </div>

    <main class="article-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <article class="article-body">
                        <?php echo $article['article']; ?>
                        <div class="text-center">
                            <a href="news.php" class="back-button">
                                <i class="fas fa-arrow-left"></i> Kembali ke Berita
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </main>
    </div>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/628989260731" class="whatsapp-float" target="_blank" title="Chat with us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>