<?php
// classes/ProductModel.php - Product Data Access Layer
class ProductModel {
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function getProductsByCategory($categoryId, $limit = null) {
        try {
            $categoryId = (int)$categoryId; // Convert to integer for safety
            $query = "SELECT id, nama, harga, foto, detail FROM produk WHERE kategori_id = $categoryId";
            
            if ($limit !== null) {
                $limit = (int)$limit;
                $query .= " LIMIT $limit";
            }

            $result = $this->database->query($query);
            
            if ($result) {
                $products = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    // Debug: check if foto field exists and has value
                    if (empty($row['foto'])) {
                        $row['foto'] = 'default-product.jpg'; // Set default image
                    }
                    $products[] = $row;
                }
                return $products;
            }
            return [];
        } catch (Exception $e) {
            error_log("Error fetching products: " . $e->getMessage());
            return [];
        }
    }

    public function searchProducts($keyword) {
        try {
            $keyword = $this->database->escape($keyword);
            $query = "SELECT id, nama, harga, foto, detail FROM produk 
                     WHERE nama LIKE '%$keyword%' OR detail LIKE '%$keyword%'";
            $result = $this->database->query($query);
            
            if ($result) {
                $products = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $products[] = $row;
                }
                return $products;
            }
            return [];
        } catch (Exception $e) {
            error_log("Error searching products: " . $e->getMessage());
            return [];
        }
    }
}
?>