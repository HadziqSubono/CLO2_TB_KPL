<?php
// classes/CategoryModel.php - Category Data Access Layer
class CategoryModel {
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function getAllCategories() {
        try {
            $query = "SELECT id, nama FROM kategori ORDER BY nama";
            $result = $this->database->query($query);
            
            if ($result) {
                $categories = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $categories[] = $row;
                }
                return $categories;
            }
            return [];
        } catch (Exception $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }

    public function getCategoryById($categoryId) {
        try {
            $categoryId = $this->database->escape($categoryId);
            $query = "SELECT id, nama FROM kategori WHERE id = '$categoryId'";
            $result = $this->database->query($query);
            
            if ($result && mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result);
            }
            return null;
        } catch (Exception $e) {
            error_log("Error fetching category: " . $e->getMessage());
            return null;
        }
    }
}
?>
