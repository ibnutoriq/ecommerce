<?php
namespace models;

use libraries\Model as Model;
use models\Order as Order;

class PaymentConfirmation extends Model
{
	private $payment_confirmations;
	const ITEM_PER_PAGE = 5;

	public function __construct()
	{

	}

	public function indexPaymentConfirmation()
	{
		$sql = 'SELECT * FROM payment_confirmations';

		$this->payment_confirmations = parent::connect()->prepare($sql);

		try {
			$this->payment_confirmations->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->payment_confirmations;
	}

	public function createPaymentConfirmation()
	{
		$req = $this->app->request();
		$sql = 'INSERT INTO payment_confirmations(order_id, bank_name, bank_account, bank_account_name, transfer_date, status, transfer_nominal, total_payment)
						VALUES(:order_id, :bank_name, :bank_account, :bank_account_name, :transfer_date, :status, :transfer_nominal, :total_payment)';
		
		$this->payment_confirmations = parent::connect()->prepare($sql);
		$data = array(
				'order_id' => $req->post('orderID'),
				'bank_name' => $req->post('bankName'),
				'bank_account' => $req->post('bankAccount'),
				'bank_account_name' => $req->post('bankAccountName'),
				'transfer_date' => $req->post('transferDate'),
				'status' => 'Unverified',
				'transfer_nominal' => $req->post('transferNominal'),
				'total_payment' => Order::showTotal(),
			);

		try {
			$this->payment_confirmations->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updatePaymentConfirmation($id)
	{
		$req = $this->app->request();
		$sql = 'UPDATE payment_confirmations SET status = :status WHERE payment_confirmation_id = :id';

		$this->payment_confirmations = parent::connect()->prepare($sql);
		$data = array(
				'id' => $id,
				'status' => $req->post('status')
			);

		try {
			$this->payment_confirmations->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deletePaymentConfirmation($id)
	{
		$req = $this->app->request();
		$sql = 'DELETE FROM payment_confirmations WHERE payment_confirmation_id = :id';

		$this->payment_confirmations = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->payment_confirmations->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function paymentConfirmationPagination($id)
	{
		$page = $id != null ? $id : 1;
		$startFrom = self::ITEM_PER_PAGE * ($page - 1);

		$sql = "SELECT * FROM payment_confirmations ORDER BY payment_confirmation_id LIMIT $startFrom, " . self::ITEM_PER_PAGE;

		$this->payment_confirmations = parent::connect()->prepare($sql);

		try {
			$this->payment_confirmations->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->payment_confirmations;
	}

	public function paymentConfirmationPage($id)
	{
		$returnPage = array();
		$page = $id != null ? $id : 1;
		$amountPage = ceil(self::indexPaymentConfirmation()->rowCount()/self::ITEM_PER_PAGE);

		for($i = 1; $i <= $amountPage; $i++) {
			if($i == $id) {
				$returnPage[] = '<li class="active"><a href="admin/payment_confirmations/page/'.$i.'">'.$i.'</a></li>';
			} else {
				$returnPage[] = '<li><a href="admin/payment_confirmations/page/'.$i.'">'.$i.'</a></li>';
			}
		}

		return $returnPage;
	}
}
?>