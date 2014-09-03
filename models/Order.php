<?php
namespace models;

use libraries\Model as Model;
use models\Cart as Cart;

class Order extends Model
{
	private $orders;
	const ITEM_PER_PAGE = 10;

	public function __construct()
	{

	}

	public function indexOrder()
	{
		$sql = 'SELECT t1.*, t2.c_email, t2.c_name, t2.address, t2.phone FROM orders t1
		INNER JOIN customers t2 ON t2.customer_id = t1.customer_id';

		$this->orders = parent::connect()->prepare($sql);

		try {
			$this->orders->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->orders;
	}

	public function showOrder()
	{
		$sql = 'SELECT t1.*, t2.c_email, t2.c_name, t2.address, t2.phone FROM orders t1
		INNER JOIN customers t2 ON t2.customer_id = t1.customer_id 
		WHERE t1.customer_id = :customer_id';

		$this->orders = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null
			);

		try {
			$this->orders->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->orders;
	}

	public function showOrderPaymentConfirmation()
	{
		$sql = 'SELECT * FROM orders WHERE customer_id = :customer_id AND status = :status';

		$this->orders = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'status' => 'Pending'
			);

		try {
			$this->orders->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->orders;
	}

	public function showTotal()
	{
		$req = $this->app->request();
		$sql = 'SELECT total FROM orders WHERE order_id = :id AND customer_id = :customer_id';

		$this->orders = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'id' => $req->post('orderID')
			);

		try {
			$this->orders->execute($data);
			$results = $this->orders->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($results as $row) {
				$data = array(
						'total' => $row['total']
					);
			}
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $data['total']; 
	}

	public function createOrder()
	{
		$sql = 'INSERT INTO orders(customer_id, status, total, created_at)
						SELECT customer_id, :status, :total, NOW() FROM customers WHERE customer_id = :customer_id';

		$this->orders = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'status' => 'Pending',
				'total' => Cart::totalCart()
			);

		try {
			$this->orders->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updateOrder($id) {
		$req = $this->app->request();
		$sql = 'UPDATE orders SET status = :status, user_id = :user_id WHERE order_id = :id';

		$this->orders = parent::connect()->prepare($sql);
		$data = array(
				'id' => $id,
				'status' => $req->post('status'),
				'user_id' => isset($_SESSION['idAdmin']) ? $_SESSION['idAdmin'] : null
			);

		try {
			$this->orders->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deleteOrder($id)
	{
		$sql = 'DELETE FROM orders WHERE order_id = :id';

		$this->orders = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->orders->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deleteOrderStatus($id)
	{
		$sql = 'DELETE FROM orders WHERE order_id = :id AND customer_id = :customer_id';

		$this->orders = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'id' => $id
			);

		try {
			$this->orders->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

	}

	public function countOrder()
	{
		$sql = 'SELECT * FROM orders WHERE customer_id = :customer_id AND status = :status';

		$this->orders = parent::connect()->prepare($sql);
		$data = array(
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'status' => 'Pending'
			);

		try {
			$this->orders->execute($data);
			$results = $this->orders->rowCount();
		} catch(PDOException $e) {
			die($e->getMessage);
		}

		return $results;
	}

	public function sumTotalOrder()
	{
		$sql = 'SELECT SUM(total) as sum_total, YEAR(created_at) as order_year, MONTH(created_at) as order_month FROM orders WHERE status != :status GROUP BY MONTH(created_at)';	
		
		$this->orders = parent::connect()->prepare($sql);
		$data = array('status' => 'Pending');

		try {
			$this->orders->execute($data);
			$results = $this->orders->fetchAll(\PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $results;
	}

	public function orderPagination($id)
	{
		$page = $id != null ? $id : 1;
		$startFrom = self::ITEM_PER_PAGE * ($page - 1);

		$sql = "SELECT t1.*, t2.c_email, t2.c_name, t2.address, t2.phone FROM orders t1
		INNER JOIN customers t2 ON t2.customer_id = t1.customer_id 
		ORDER BY order_id LIMIT $startFrom, " . self::ITEM_PER_PAGE;

		$this->orders = parent::connect()->prepare($sql);

		try {
			$this->orders->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->orders;
	}

	public function orderPage($id)
	{
		$returnPage = array();
		$page = $id != null ? $id : 1;
		$amountPage = ceil(self::indexOrder()->rowCount()/self::ITEM_PER_PAGE);

		for($i = 1; $i <= $amountPage; $i++) {
			if($i == $id) {
				$returnPage[] = '<li class="active"><a href="admin/orders/page/'.$i.'">'.$i.'</a></li>';
			} else {
				$returnPage[] = '<li><a href="admin/orders/page/'.$i.'">'.$i.'</a></li>';
			}
		}

		return $returnPage;
	}

	public function salesReport()
	{
		$sql = "SELECT * FROM orders WHERE status != 'In Process'";

		$this->orders = parent::connect()->prepare($sql);

		try {
			$this->orders->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->orders;
	}

	public function salesReportPagination($id)
	{
		$page = $id != null ? $id : 1;
		$startFrom = self::ITEM_PER_PAGE * ($page - 1);

		$sql = "SELECT * FROM orders WHERE status != 'In Process' LIMIT $startFrom, " . self::ITEM_PER_PAGE;

		$this->orders = parent::connect()->prepare($sql);

		try {
			$this->orders->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->orders;
	}

	public function salesReportPage($id)
	{
		$returnPage = array();
		$page = $id != null ? $id : 1;
		$amountPage = ceil(self::salesReport()->rowCount()/self::ITEM_PER_PAGE);

		for($i = 1; $i <= $amountPage; $i++) {
			if($i == $id) {
				$returnPage[] = '<li class="active"><a href="admin/sales_report/page/'.$i.'">'.$i.'</a></li>';
			} else {
				$returnPage[] = '<li><a href="admin/sales_report/page/'.$i.'">'.$i.'</a></li>';
			}
		}

		return $returnPage;	
	}

	public function salesReportByDate()
	{
		$req = $this->app->request();
		$sql = "SELECT * FROM orders WHERE status != 'In Process' AND created_at BETWEEN DATE(:createdAt1) AND DATE(:createdAt2) + INTERVAL 1 DAY";

		$this->orders = parent::connect()->prepare($sql);

		$data = array(
				'createdAt1' => $req->post('createdAt1'),
				'createdAt2' => $req->post('createdAt2')
			);

		try {
			$this->orders->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->orders;
	}

	public function grandTotalSalesReport()
	{
		$sql = "SELECT SUM(total) as grand_total FROM orders WHERE status != 'In Process'";

		$this->orders = parent::connect()->prepare($sql);

		try {
			$this->orders->execute();

			foreach($this->orders as $row) {
				$results = $row['grand_total'];
			}
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $results;
	}

	public function grandTotalSalesReportByDate()
	{
		$req = $this->app->request();
		$sql = "SELECT SUM(total) as grand_total FROM orders WHERE status != 'In Process' AND created_at BETWEEN DATE(:createdAt1) AND DATE(:createdAt2) + INTERVAL 1 DAY";

		$this->orders = parent::connect()->prepare($sql);

		$data = array(
				'createdAt1' => $req->post('createdAt1'),
				'createdAt2' => $req->post('createdAt2')
			);

		try {
			$this->orders->execute($data);

			foreach($this->orders as $row) {
				$results = $row['grand_total'];
			}
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $results;
	}

}
?>