<?php
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Brand.php';
require_once __DIR__ . '/../models/Core.php'; // For direct DB access if needed

class PosController {
    private $categoryModel;
    private $brandModel;

    public function __construct() {
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
    }

    public function getInitialData($shop_id) {
        return [
            'categories' => $this->categoryModel->getAll($shop_id)->fetch_all(MYSQLI_ASSOC),
            'brands' => $this->brandModel->getAll($shop_id)->fetch_all(MYSQLI_ASSOC)
        ];
    }
}
?>
