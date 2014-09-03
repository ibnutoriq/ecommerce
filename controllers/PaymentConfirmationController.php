<?php
namespace controllers;

use libraries\Controller as Controller;
use models\Cart as Cart;
use models\Order as Order;
use models\PaymentConfirmation as PaymentConfirmation;

class PaymentConfirmationController extends Controller
{
	public function __construct()
	{

	}

	public function index($id = null)
	{
		parent::routes()->render('index_payment_confirmation.twig', array(
				'app_base' => $this->appBase,
				'payment_confirmation_page' => PaymentConfirmation::paymentConfirmationPage($id),
				'results' => PaymentConfirmation::paymentConfirmationPagination($id),
				'title' => 'Payment Confirmations'
			));
	}

	public function add()
	{
		parent::routes()->render('new_payment_confirmation.twig', array(
				'app_base' => $this->appBase,
				'carts' => Cart::countCart(),
				'is_customer' => isset($_SESSION['emailCustomer']) ? sizeof($_SESSION['emailCustomer']) : 0,
				'is_order' => Order::countOrder(),
				'orders' => Order::showOrderPaymentConfirmation(),
				'title' => 'Payment Confirmation'
			));
	}

	public function create()
	{
		PaymentConfirmation::createPaymentConfirmation();
		parent::redirectTo('newPaymentConfirmation');
	}

	public function update($id)
	{
		PaymentConfirmation::updatePaymentConfirmation($id);
		parent::redirectTo('indexPaymentConfirmation');
	}

	public function delete($id)
	{
		PaymentConfirmation::deletePaymentConfirmation($id);
		parent::redirectTo('indexPaymentConfirmation');
	}
}
?>