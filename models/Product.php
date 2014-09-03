<?php
namespace models;

use libraries\Model as Model;

class Product extends Model
{
	private $products;
	const ITEM_PER_PAGE = 5;

	public function __construct()
	{

	}

	public function indexProduct()
	{
		$sql = 'SELECT * FROM products WHERE stock >= 5';

		$this->products = parent::connect()->prepare($sql);

		try {
			$this->products->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->products;
	}

	public function indexProductByStock()
	{
		$sql = 'SELECT * FROM products WHERE stock != 0';

		$this->products = parent::connect()->prepare($sql);
		$data = array('stock' => 'Available');

		try {
			$this->products->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->products;
	}

	public function showProduct($id)
	{
		$sql = 'SELECT * FROM products WHERE product_id = :id';

		$this->products = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->products->execute($data);
			$results = $this->products->fetchAll();
			$this->products->closeCursor();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $results;
	}

	public function showProductName($id)
	{
		$sql = 'SELECT p_name FROM products WHERE product_id = :id';

		$this->products = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->products->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		if ($this->products->rowCount() > 0) {
			foreach($this->products as $row) {
				$results = $row['p_name'];
			}
		} else {
			$results = 'Not found';
		}

		return $results;
	}

	public function showImageProduct($id)
	{
		$sql = 'SELECT p_image FROM products WHERE product_id = :id';

		$this->products = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->products->execute($data);
			$results = $this->products->fetchColumn();
			$this->products->closeCursor();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $results;
	}

	public function showRelatedProduct($category, $id)
	{
		$sql = 'SELECT product_id, p_name, p_image FROM products WHERE category = :category AND product_id != :id LIMIT 5';

		foreach($category as $row) {
			$results = array( 'category' => $row['category']);
		}

		$this->products = parent::connect()->prepare($sql);
		$data = array(
			'category' => $results['category'],
			'id' => $id
			);

		try {
			$this->products->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->products;
	}

	public function showProductCategory($id)
	{
		$sql = 'SELECT category FROM products WHERE product_id = :id';

		$this->products = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->products->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->products;	
	}

	public function createProduct()
	{
		$req = $this->app->request();
		$imageName = $_FILES['image']['name'];
		$imageTmp = $_FILES['image']['tmp_name'];
		$uniqueID = md5(uniqid(rand(), true));
		$fileType = strchr($imageName, '.');
		$newUpload = 'assets/img_public/' . $uniqueID . $fileType;

		move_uploaded_file($imageTmp, $newUpload);
		@chmod($newUpload, 0777);

		$sql = 'INSERT INTO products(p_name, description, category, p_image, price, stock) 
						VALUES(:p_name, :description, :category, :p_image, :price, :stock)';

		$this->products = parent::connect()->prepare($sql);
		$data = array(
				'p_name' => $req->post('name'),
				'description' => $req->post('description'),
				'category' => $req->post('category'),
				'p_image' => $newUpload,
				'price' => $req->post('price'),
				'stock' => $req->post('stock')		
			);
		

		try {
			$this->products->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updateProduct($id)
	{
		$req = $this->app->request();
		$imageName = $_FILES['image']['name'];
		$imageTmp = $_FILES['image']['tmp_name'];
		$uniqueID = md5(uniqid(rand(), true));
		$fileType = strchr($imageName, '.');
		$newUpload = 'assets/img_public/' . $uniqueID . $fileType;

		if($imageName != null) {
			unlink(Product::showImageProduct($id));
		}	

		move_uploaded_file($imageTmp, $newUpload);
		@chmod($newUpload, 0777);

		if($imageName != null) {
			$sql = 'UPDATE products SET p_name = :p_name, description = :description, category = :category, 
							p_image = :p_image, price = :price, stock = :stock WHERE product_id = :id';
		} else {
			$sql = 'UPDATE products SET p_name = :p_name, description = :description, category = :category, 
						  price = :price, stock = :stock WHERE product_id = :id';
		}

		$this->products = parent::connect()->prepare($sql);
		$this->products->bindValue(':p_name', $req->post('name'));		
		$this->products->bindValue(':description', $req->post('description'));
		$this->products->bindValue(':category', $req->post('category'));
		if($imageName != null) {
			$this->products->bindValue(':p_image', $newUpload);
		}			
		$this->products->bindValue(':price', $req->post('price'));		
		$this->products->bindValue(':stock', $req->post('stock'));		
		$this->products->bindValue(':id', $id);		
		
		try {
			$this->products->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updateStockProduct($id)
	{
		$sql = 'UPDATE products t1 INNER JOIN order_details t2
			ON t2.product_id = t1.product_id SET t1.stock = t1.stock - t2.quantity
			WHERE t2.order_id = :id';

		$this->products = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->products->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deleteProduct($id)
	{
		unlink(Product::showImageProduct($id));	
		
		$sql = 'DELETE FROM products WHERE product_id = :id';

		$this->products = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->products->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function ProductPagination($id)
	{
		$page = $id != null ? $id : 1;
		$startFrom = self::ITEM_PER_PAGE * ($page - 1);

		$sql = "SELECT * FROM products ORDER BY p_name LIMIT $startFrom, " . self::ITEM_PER_PAGE;

		$this->products = parent::connect()->prepare($sql);

		try {
			$this->products->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->products;
	}

	public function ProductPage($id, $init = 'admin')
	{
		$returnPage = array();
		$page = $id != null ? $id : 1;
		$amountPage = ceil(self::indexProduct()->rowCount()/self::ITEM_PER_PAGE);

		for($i = 1; $i <= $amountPage; $i++) {
			if ($init == 'admin') {
				if($i == $id ) {
					$returnPage[] = '<li class="active"><a href="admin/products/page/'.$i.'">'.$i.'</a></li>';
				} else {
					$returnPage[] = '<li><a href="admin/products/page/'.$i.'">'.$i.'</a></li>';
				}
			} else {
				if($i == $id ) {
					$returnPage[] = '<li class="active"><a href="page/'.$i.'">'.$i.'</a></li>';
				} else {
					$returnPage[] = '<li><a href="page/'.$i.'">'.$i.'</a></li>';
				}
			}
		}

		return $returnPage;
	}
}
?>