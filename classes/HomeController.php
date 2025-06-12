<?php
// classes/HomeController.php - Business Logic Layer
class HomeController {
    private $categoryModel;
    private $productModel;
    private $languageHelper;
    private $currencyHelper;

    public function __construct(
        CategoryModel $categoryModel,
        ProductModel $productModel,
        $languageHelper,
        CurrencyHelper $currencyHelper
    ) {
        $this->categoryModel = $categoryModel;
        $this->productModel = $productModel;
        $this->languageHelper = $languageHelper;
        $this->currencyHelper = $currencyHelper;
    }

    public function handleLanguageSwitch() {
        // Language switching is handled by existing language.php
        // This method can be used for additional processing if needed
        return true;
    }

    public function getHomePageData() {
        try {
            $categories = $this->categoryModel->getAllCategories();
            $categoriesWithProducts = [];

            foreach ($categories as $category) {
                $products = $this->productModel->getProductsByCategory($category['id'], 3);
                
                if (!empty($products)) {
                    $categoriesWithProducts[] = [
                        'category' => $category,
                        'products' => $this->formatProductsForDisplay($products)
                    ];
                }
            }

            return [
                'categories' => $categories,
                'categoriesWithProducts' => $categoriesWithProducts,
                'highlightedCategories' => $this->getHighlightedCategories(),
                'currentLanguage' => $this->languageHelper->getCurrentLang()
            ];
        } catch (Exception $e) {
            error_log("Error in getHomePageData: " . $e->getMessage());
            return [
                'categories' => [],
                'categoriesWithProducts' => [],
                'highlightedCategories' => $this->getHighlightedCategories(),
                'currentLanguage' => 'id'
            ];
        }
    }

    private function formatProductsForDisplay($products) {
        $formattedProducts = [];
        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product['id'],
                'nama' => $product['nama'],
                'harga' => $this->currencyHelper->formatRupiah($product['harga']),
                'foto' => !empty($product['foto']) ? $product['foto'] : 'default-product.jpg',
                'detail' => $product['detail']
            ];
        }
        return $formattedProducts;
    }

    private function getHighlightedCategories() {
        return [
            ['name' => 'Jujutsu Kaisen', 'class' => 'kategori-1'],
            ['name' => 'Genshin Impact', 'class' => 'kategori-2'],
            ['name' => 'Demon Slayer', 'class' => 'kategori-3']
        ];
    }
}
?>