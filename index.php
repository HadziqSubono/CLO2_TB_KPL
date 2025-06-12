<?php
// index.php - Main Entry Point
session_start();
require_once "koneksi.php";
require_once "language.php";

// Include all necessary classes
require_once "classes/Database.php";
require_once "classes/CategoryModel.php";
require_once "classes/ProductModel.php";
require_once "classes/HomeController.php";
require_once "classes/CurrencyHelper.php";

// Initialize dependencies
$database = new Database($con); // Using existing connection
$categoryModel = new CategoryModel($database);
$productModel = new ProductModel($database);
$currencyHelper = new CurrencyHelper();

// Initialize controller
$homeController = new HomeController(
    $categoryModel,
    $productModel,
    $lang, // Using existing language helper
    $currencyHelper
);

// Handle language switching
$homeController->handleLanguageSwitch();

// Get data for view
$viewData = $homeController->getHomePageData();
?>

<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('site.title'); ?> | <?php echo __('nav.home'); ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .produk-col {
            margin-bottom: 1rem;
        }
        .card:hover {
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .language-switcher a.active {
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
        }
        .no-decoration {
            text-decoration: none;
        }
        .image-box {
            height: 200px;
            overflow: hidden;
        }
        .image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Language Switcher -->
    <div class="language-switcher position-absolute top-0 end-0 m-3" style="z-index: 1000;">
        <a href="?lang=id" class="<?php echo $lang->getCurrentLang() == 'id' ? 'active' : ''; ?>">ID</a> |
        <a href="?lang=en" class="<?php echo $lang->getCurrentLang() == 'en' ? 'active' : ''; ?>">EN</a> |
        <a href="?lang=jp" class="<?php echo $lang->getCurrentLang() == 'jp' ? 'active' : ''; ?>">JP</a>
    </div>

    <!-- Navbar -->
    <?php require "navbar.php"; ?>
    
    <!-- Banner Section -->
    <div class="container-fluid banner d-flex align-items-center">
        <div class="container text-center text-white">
            <h1><?php echo __('site.title'); ?><i class="ms-2 fa-solid fa-paw"></i></h1>
            <div class="col-md-8 offset-md-2">
                <form action="produk.php" method="get">
                    <input type="hidden" name="lang" value="<?php echo $lang->getCurrentLang(); ?>">
                    <div class="input-group input-group-lg my-4">
                        <input type="text" class="form-control" 
                               placeholder="<?php echo __('search.placeholder'); ?>" 
                               name="keyword" 
                               aria-label="Search products">
                        <button type="submit" class="btn warna2 text-white">
                            <?php echo __('search.button'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Highlighted Categories -->
    <div class="container-fluid py-5">
        <div class="container text-center">
            <h3><?php echo __('categories.title'); ?></h3>
            <div class="row mt-5">
                <?php foreach($viewData['highlightedCategories'] as $category): ?>
                    <div class="col-md-4 mb-3">
                        <div class="highlighted-kategori <?php echo $category['class']; ?> d-flex justify-content-center align-items-center">
                            <h4 class="text-white">
                                <a class="no-decoration" href="produk.php?kategori=<?php echo urlencode($category['name']); ?>&lang=<?php echo $lang->getCurrentLang(); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </h4>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div class="container-fluid warna3 py-5">
        <div class="container text-center text-light">
            <h3><?php echo __('about.title'); ?></h3>
            <p class="fs-5 mt-3"><?php echo __('about.description'); ?></p>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container-fluid py-5">
        <div class="container text-center">
            <h3><?php echo __('products.title'); ?></h3>
            <?php if (!empty($viewData['categoriesWithProducts'])): ?>
                <div class="row mt-5">
                    <?php foreach($viewData['categoriesWithProducts'] as $categoryData): ?>
                        <div class="col-12 mb-4">
                            <h4 class="text-start"><?php echo htmlspecialchars($categoryData['category']['nama']); ?></h4>
                        </div>
                        <?php foreach($categoryData['products'] as $product): ?>
                            <div class="col-sm-6 col-md-4 mb-3 produk-col">
                                <div class="card h-100 shadow">
                                    <div class="image-box">
                                        <?php 
                                        // FIXED: Changed image path to include 'produk' folder
                                        $imagePath = "image/produk/" . $product['foto'];
                                        if (!file_exists($imagePath)) {
                                            $imagePath = "image/default-product.jpg";
                                        }
                                        ?>
                                        <img src="<?php echo $imagePath; ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($product['nama']); ?>"
                                             loading="lazy"
                                             onerror="this.src='image/default-product.jpg'"
                                             style="width: 100%; height: 200px; object-fit: cover;">
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['nama']); ?></h5>
                                        <p class="card-text text-truncate flex-grow-1"><?php echo htmlspecialchars($product['detail']); ?></p>
                                        <p class="card-text text-harga fw-bold text-primary"><?php echo $product['harga']; ?></p>
                                        <a href="produk-detail.php?id=<?php echo $product['id']; ?>&lang=<?php echo $lang->getCurrentLang(); ?>" 
                                           class="btn warna2 text-white mt-auto">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-5">
                    <p><?php echo __('products.no_products') ?: 'Belum ada produk tersedia.'; ?></p>
                </div>
            <?php endif; ?>
            
            <a href="produk.php?lang=<?php echo $lang->getCurrentLang(); ?>" 
               class="btn btn-outline-warning mt-3 p-3 fs-6">
                <?php echo __('products.see_more') ?: 'Lihat Semua Produk'; ?>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <?php require "footer.php"; ?>
    
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fontawesome/js/all.min.js"></script>
</body>
</html>