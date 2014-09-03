<?php
namespace models;

use libraries\Model as Model;

class OrderDetail extends Model
{
	private $order_details;

	public function __construct()
	{

	}

	public function showOrderDetail($id)
	{
		$sql = 'SELECT t1.*, t2.p_name, t1.quantity, t2.price FROM order_details t1 
			INNER JOIN products t2 ON t2.product_id = t1.product_id 
			WHERE t1.order_id = :id';

		$this->order_details = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->order_details->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage);
		}

		return $this->order_details;
	}

	public function createOrderDetail()
	{
		$sql = 'INSERT INTO order_details(order_id, product_id, quantity)
						SELECT t2.order_id, t1.product_id, t1.quantity FROM carts t1
						INNER JOIN orders t2 ON t2.customer_id = t1.customer_id
						WHERE t1.customer_id = :customer_id and t2.customer_id = :customer_id';

		$this->order_details = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null
			);

		try {
			$this->order_details->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage);
		}
	}
}
?>