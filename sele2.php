<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RetroBridge - Sunset Phase</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a0a2e 25%, #16213e 50%, #0f3460 75%, #533483 100%);
        min-height: 100vh;
        color: white;
        overflow-x: hidden;
    }
    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;
        position: relative;
        z-index: 10;
    }
    .logo {
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 2px;
    }
    .launch-btn {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        padding: 12px 24px;
        border-radius: 25px;
        border: none;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .launch-btn:hover {
        transform: translateY(-2px);
    }
    .main-content {
        margin-top: 50px;
        text-align: center;
    }
    .subtitle {
        color: #4ecdc4;
        font-size: 14px;
        letter-spacing: 2px;
        margin-bottom: 20px;
        text-transform: uppercase;
    }
    .main-title {
        font-size: 48px;
        font-weight: bold;
        line-height: 1.1;
        margin-bottom: 20px;
        background: linear-gradient(135deg, #ffffff, #e0e0e0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .year {
        font-size: 20px;
        color: #888;
        margin-bottom: 30px;
    }
    .carousel-container {
        position: relative;
        overflow: hidden;
        width: 100%;
    }
    .nft-cards {
        display: flex;
        gap: 20px;
        transition: transform 0.4s ease;
    }
    .nft-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        flex-shrink: 0;
        width: 200px;
        transition: all 0.4s ease;
        text-align: center;
    }
    .nft-card.active {
        transform: scale(1.2);
        opacity: 1;
        z-index: 2;
    }
    .nft-card:not(.active) {
        transform: scale(0.9);
        opacity: 0.6;
        z-index: 1;
    }
    .nft-image {
        width: 100%;
        height: 120px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        border-radius: 15px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .nft-sphere {
        width: 80px;
        height: 80px;
        background: radial-gradient(circle at 30% 30%, #ff9ff3, #ff6b6b, #ee5a24);
        border-radius: 50%;
        box-shadow: 0 0 20px rgba(255, 107, 107, 0.8);
        animation: float 3s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 5;
        font-size: 24px;
        color: white;
        user-select: none;
    }
    .carousel-nav.prev { left: 0; }
    .carousel-nav.next { right: 0; }
</style>
</head>
<body>
<div class="container">
    <header class="header">
        <div class="logo">RETROBRIDGE</div>
        <button class="launch-btn">Launch App</button>
    </header>

    <main class="main-content">
        <div class="subtitle">Complete Sunset Journey and Get 3 of 4 NFT</div>
        <h1 class="main-title">Sunset Phase</h1>
        <div class="year">(Late 2024)</div>

        <div class="carousel-container">
            <div class="carousel-nav prev" onclick="slideCarousel(-1)">&#10094;</div>
            <div class="carousel-nav next" onclick="slideCarousel(1)">&#10095;</div>

            <div class="nft-cards" id="nftCarousel">
                <div class="nft-card">
                    <div class="nft-image"><div class="nft-sphere"></div></div>
                    <div class="nft-title">Card 1</div>
                </div>
                <div class="nft-card">
                    <div class="nft-image"><div class="nft-sphere"></div></div>
                    <div class="nft-title">Card 2</div>
                </div>
                <div class="nft-card">
                    <div class="nft-image"><div class="nft-sphere"></div></div>
                    <div class="nft-title">Card 3</div>
                </div>
                <div class="nft-card">
                    <div class="nft-image"><div class="nft-sphere"></div></div>
                    <div class="nft-title">Card 4</div>
                </div>
                <div class="nft-card">
                    <div class="nft-image"><div class="nft-sphere"></div></div>
                    <div class="nft-title">Card 5</div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
let activeIndex = 0;

function updateCarousel() {
    const cards = document.querySelectorAll('.nft-card');
    const cardWidth = 220; // lebar + gap
    const carousel = document.getElementById('nftCarousel');

    const offset = (carousel.parentElement.offsetWidth / 2) - (cardWidth / 2) - (activeIndex * cardWidth);
    carousel.style.transform = `translateX(${offset}px)`;

    cards.forEach((card, index) => {
        card.classList.toggle('active', index === activeIndex);
    });
}

function slideCarousel(direction) {
    const cards = document.querySelectorAll('.nft-card');
    activeIndex = (activeIndex + direction + cards.length) % cards.length;
    updateCarousel();
}

document.querySelectorAll('.nft-card').forEach((card, index) => {
    card.addEventListener('click', () => {
        activeIndex = index;
        updateCarousel();
    });
});

document.addEventListener('DOMContentLoaded', () => {
    updateCarousel();
});

// Swipe support
let touchStartX = 0;
document.getElementById('nftCarousel').addEventListener('touchstart', e => {
    touchStartX = e.touches[0].clientX;
});
document.getElementById('nftCarousel').addEventListener('touchend', e => {
    const touchEndX = e.changedTouches[0].clientX;
    if (touchStartX - touchEndX > 50) slideCarousel(1);
    if (touchEndX - touchStartX > 50) slideCarousel(-1);
});
</script>
</body>
</html>
