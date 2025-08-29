<?php
// Determine active page
if (!isset($activePage)) {
	$script = basename($_SERVER['PHP_SELF']);
	$activePage = strtolower(str_replace('.php', '', $script));
}

function isActive($page, $activePage) {
	return $page === $activePage ? ' active' : '';
}
?>
<nav class="navbar navbar-expand-lg">
	<div class="container">
		<a class="navbar-brand" href="index.php"><img src="property/logo idspora_nobg_outlined.png" alt="idSpora Logo" /></a>

		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav ms-auto me-4">
				<li class="nav-item">
					<a class="nav-link<?= isActive('index', $activePage); ?>" href="index.php">Beranda</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?= isActive('about', $activePage); ?>" href="about.php">Tentang</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?= isActive('products', $activePage); ?>" href="products.php">Portofolio</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?= isActive('reviews', $activePage); ?>" href="reviews.php">Ulasan</a>
				</li>
				<li class="nav-item">
					<a class="nav-link<?= isActive('news', $activePage); ?>" href="news.php">Berita</a>
				</li>
			</ul>
			<a href="contact.php" class="btn btn-contact">Hubungi Kami</a>
		</div>
	</div>
</nav>
