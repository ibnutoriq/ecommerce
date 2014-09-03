<?php
namespace models;

use libraries\Model as Model;
use libraries\Bcrypt as Bcrypt;

class Customer extends Model
{
	private $customers;
	const ITEM_PER_PAGE = 5;

	public function __construct()
	{
		
	}

	public function indexCustomer()
	{
		$sql = 'SELECT * FROM customers';

		$this->customers = parent::connect()->prepare($sql);

		try {
			$this->customers->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->customers;
	}

	public function showCustomer($id)
	{
		$sql = 'SELECT * FROM customers WHERE customer_id = :id';

		$this->customers = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->customers->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->customers;
	}

	public function showCustomerByEmail($c_email)
	{
		$sql = 'SELECT * FROM customers WHERE c_email = :c_email';

		$this->customers = parent::connect()->prepare($sql);
		$data = array('c_email' => $c_email);

		try {
			$this->customers->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->customers;
	}

	public function createCustomer()
	{
		$req = $this->app->request();

		$sql = 'INSERT INTO customers(c_email, c_password, c_name, address, phone, notes) 
						VALUES(:c_email, :c_password, :c_name, :address, :phone, :notes)';
		
		$this->customers = parent::connect()->prepare($sql);

		$data = array(
				'c_email' => $req->post('c_email'),
				'c_password' => Bcrypt::hash($req->post('c_password')),
				'c_name' => $req->post('c_name'),
				'address' => $req->post('address'),
				'phone' => $req->post('phone'),
				'notes' => $req->post('notes')
			);
		
		try {
			$this->customers->execute($data);			
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updateCustomer($id)
	{
		$req = $this->app->request();
		
		$sql = 'UPDATE customers SET c_email = :c_email, c_password = :c_password, c_name = :c_name, address = :address, phone = :phone, notes = :notes 
			WHERE customer_id = :id';
		
		$this->customers = parent::connect()->prepare($sql);
		$data = array(
				'c_email' => $req->post('c_email'),
				'c_password' => Bcrypt::hash($req->post('c_password')),
				'c_name' => $req->post('c_name'),
				'address' => $req->post('address'),
				'phone' => $req->post('phone'),
				'notes' => $req->post('notes'),
				'id' => $id		
			);

		try {
			$this->customers->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updateCustomerProfile($id)
	{
		$req = $this->app->request();
		
		$sql = 'UPDATE customers SET c_name = :c_name, address = :address, phone = :phone, notes = :notes
			WHERE customer_id = :id';
		
		$this->customers = parent::connect()->prepare($sql);
		$data = array(
				'c_name' => $req->post('c_name'),
				'address' => $req->post('address'),
				'phone' => $req->post('phone'),
				'notes' => $req->post('notes'),
				'id' => $id		
			);

		try {
			$this->customers->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updateNotes()
	{
		$req = $this->app->request();
		$sql = 'UPDATE customers SET notes = :notes WHERE customer_id = :id';

		$this->customers = parent::connect()->prepare($sql);
		$data = array(
				'id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'notes' => $req->post('notes')
			);

		try {
			$this->customers->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deleteCustomer($id)
	{
		$sql = 'DELETE FROM customers WHERE customer_id = :id';

		$this->customers = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->customers->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function countCustomer($c_email)
	{
		$sql = 'SELECT COUNT(*) FROM customers WHERE c_email = :c_email';

		$this->customers = parent::connect()->prepare($sql);
		$data = array('c_email' => $c_email);

		try {
			$this->customers->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->customers;
	}

	public function CustomerPagination($id)
	{
		$page = $id != null ? $id : 1;
		$startFrom = self::ITEM_PER_PAGE * ($page - 1);

		$sql = "SELECT * FROM customers ORDER BY c_name LIMIT $startFrom, " . self::ITEM_PER_PAGE;

		$this->customers = parent::connect()->prepare($sql);

		try {
			$this->customers->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->customers;
	}

	public function CustomerPage($id)
	{
		$returnPage = array();
		$page = $id != null ? $id : 1;
		$amountPage = ceil(self::indexCustomer()->rowCount()/self::ITEM_PER_PAGE);

		for($i = 1; $i <= $amountPage; $i++) {
			if($i == $id) {
				$returnPage[] = '<li class="active"><a href="admin/customers/page/'.$i.'">'.$i.'</a></li>';
			} else {
				$returnPage[] = '<li><a href="admin/customers/page/'.$i.'">'.$i.'</a></li>';
			}
		}

		return $returnPage;
	}
}
?>