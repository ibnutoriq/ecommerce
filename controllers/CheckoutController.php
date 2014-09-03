<?php
namespace controllers;

use libraries\Controller as Controller;
use models\Cart as Cart;
use models\Customer as Customer;
use models\Order as Order;
use models\OrderDetail as OrderDetail;

class CheckoutController extends Controller
{
	public function __construct()
	{

	}

	public function index()
	{
		parent::routes()->render('index_checkout.twig', array(
				'app_base' => $this->appBase,
				'carts' => Cart::countCart(),
				'customers' => Customer::showCustomer(isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null),
				'is_customer' => isset($_SESSION['emailCustomer']) ? sizeof($_SESSION['emailCustomer']) : 0,
				'results' => Cart::indexCart(),
				'title' => 'Checkout',
				'total' => Cart::totalCart()
			));
	}

	public function create()
	{
		Order::createOrder();
		Customer::updateNotes();
		OrderDetail::createOrderDetail();
		Cart::deleteCartAfterCheckout();
		parent::redirectTo('indexHome');
	}
}
?>