<?php
namespace models;

use libraries\Model as Model;

class Cart extends Model
{
	private $carts;

	public function __construct()
	{

	}

	public function indexCart()
	{
		$sql = 'SELECT t1.cart_id, t2.p_name, t2.p_image, t2.price, t1.quantity FROM carts t1
			INNER JOIN products t2 ON t2.product_id = t1.product_id
			WHERE t1.customer_id = :customer_id';

		$this->carts = parent::connect()->prepare($sql);
		$data = array('customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null);

		try {
			$this->carts->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->carts;
	}

	public function createCart()
	{
		$req = $this->app->request();

		$sql = 'INSERT INTO carts(customer_id, product_id, ip, quantity, total_price)
			SELECT :customer_id, product_id, :ip, :quantity, (:quantity * price) FROM products 
			WHERE product_id = :id';

		$this->carts = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'id' => $req->post('productID'),
				'ip' => $_SERVER['REMOTE_ADDR'],
				'quantity' => $req->post('quantity')
			);

		try {
			$this->carts->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updateCart($id)
	{
		$req = $this->app->request();
		$sql = 'UPDATE carts t1 INNER JOIN products t2 ON t2.product_id = t1.product_id
		SET t1.quantity = :quantity, t1.total_price = (:quantity * t2.price) 
		WHERE t1.cart_id = :id AND t1.customer_id  = :customer_id';

		$this->carts = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'id' => $id,
				'quantity' => $req->post('quantity')
			);

		try {
			$this->carts->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deleteCartAfterCheckout()
	{
		$sql = 'DELETE FROM carts WHERE customer_id = :customer_id';

		$this->carts = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null
			);

		try {
			$this->carts->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deleteCart($id)
	{
		$sql = 'DELETE FROM carts WHERE cart_id = :id AND customer_id = :customer_id';

		$this->carts = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'id' => $id,
			);

		try {
			$this->carts->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function countCart()
	{
		$sql = 'SELECT * FROM carts WHERE customer_id = :customer_id';

		$this->carts = parent::connect()->prepare($sql);
		$data = array('customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null);

		try {
			$this->carts->execute($data);
			$results = $this->carts->rowCount();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $results;
	}

	public function totalCart()
	{
		$sql = 'SELECT SUM(total_price) as total_cart FROM carts WHERE customer_id = :customer_id';

		$this->carts = parent::connect()->prepare($sql);
		$data = array('customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null);

		try {
			$this->carts->execute($data);
			$results = $this->carts->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($results as $row) {
				$data = array(
						'total_cart' => $row['total_cart']
					);
			}
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $data['total_cart'];
	}
}
?>