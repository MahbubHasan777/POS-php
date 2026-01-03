<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Brand.php';

class InventoryController {
    private $productModel;
    private $categoryModel;
    private $brandModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
    }

    // Product Methods
    public function getProducts($shop_id, $search = '', $category_id = null) {
        return $this->productModel->getAll($shop_id, $search, $category_id);
    }

    public function getProduct($id, $shop_id) {
        return $this->productModel->get($id, $shop_id);
    }

    public function saveProduct($data, $shop_id, $id = null) {
        // Basic validation could happen here
        return $this->productModel->save($data, $shop_id, $id);
    }

    // Category Methods
    public function getCategories($shop_id) {
        return $this->categoryModel->getAll($shop_id);
    }

    public function saveCategory($name, $shop_id, $id = null) {
        return $this->categoryModel->save($name, $shop_id, $id);
    }

    public function deleteCategory($id, $shop_id) {
        return $this->categoryModel->delete($id, $shop_id);
    }

    // Brand Methods
    public function getBrands($shop_id) {
        return $this->brandModel->getAll($shop_id);
    }

    public function saveBrand($name, $shop_id, $id = null) {
        return $this->brandModel->save($name, $shop_id, $id);
    }

    public function deleteBrand($id, $shop_id) {
        return $this->brandModel->delete($id, $shop_id);
    }
}
?>
