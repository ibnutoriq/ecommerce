<?php
namespace controllers;

use libraries\Controller as Controller;
use models\Cart as Cart;
use models\Order as Order;
use models\OrderDetail as OrderDetail;
use models\Product as Product;

class OrderController extends Controller
{
	public function __construct()
	{

	}

	public function index($id = null)
	{
		parent::routes()->render('index_order.twig', array(
			'app_base' => $this->appBase,
			'order_page' => Order::orderPage($id),
			'results' => Order::orderPagination($id),
			'title' => 'Orders'
		));
	}

	public function update($id)
	{
		Order::updateOrder($id);
		Product::updateStockProduct($id);
		parent::redirectTo('indexOrder');
	}

	public function delete($id)
	{
		Order::deleteOrder($id);
		parent::redirectTo('indexOrder');
	}

	public function orderStatus()
	{
		parent::routes()->render('order_status.twig', array(
				'app_base' => $this->appBase,
				'carts' => Cart::countCart(),
				'is_customer' => isset($_SESSION['emailCustomer']) ? sizeof($_SESSION['emailCustomer']) : 0,
				'results' => Order::showOrder(),
				'title' => 'Your Orders'
			));
	}

	public function deleteOrderStatus($id)
	{
		Order::deleteOrderStatus($id);
		parent::redirectTo('indexOrderStatus');
	}

	public function showOrderDetail($init, $id)
	{
		if ($init == 'admin') {
			$idData = explode('/', $_SERVER['REQUEST_URI']);
			$idAny = end($idData);

			parent::routes()->render('admin_order_detail.twig', array(
					'app_base' => $this->appBase,
					'results' => OrderDetail::showOrderDetail($id),
					'title' => 'Order Details #' . $idAny 
				));
		} else {
			parent::routes()->render('order_detail.twig', array(
					'app_base' => $this->appBase,
					'carts' => Cart::countCart(),
					'is_customer' => isset($_SESSION['emailCustomer']) ? sizeof($_SESSION['emailCustomer']) : 0,
					'is_order' => Order::countOrder(),
					'results' => OrderDetail::showOrderDetail($id),
					'title' => 'Your Order Details'
				));
		}
	}
}
?>