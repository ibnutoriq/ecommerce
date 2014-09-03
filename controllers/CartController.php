<?php
namespace controllers;

use libraries\Controller as Controller;
use models\Cart as Cart;

class CartController extends Controller
{
	public function __construct()
	{

	}

	public function index()
	{
		parent::routes()->render('index_cart.twig', array(
				'app_base' => $this->appBase,
				'carts' => Cart::countCart(),
				'is_customer' => isset($_SESSION['emailCustomer']) ? sizeof($_SESSION['emailCustomer']) : 0,
				'results' => Cart::indexCart(),
				'title' => 'Your Carts',
				'total' => Cart::totalCart()
			));
	}

	public function create()
	{
		Cart::createCart();
		$this->app->flash('infoCart', 'Product has been added to cart');
		$this->app->redirect($this->appBase . '/products/show/' . $this->app->request()->post('productID'));
	}

	public function update($id)
	{
		Cart::updateCart($id);
		parent::redirectTo('cart');
	}

	public function delete($id)
	{
		Cart::deleteCart($id);
		parent::redirectTo('cart');
	}
}
?>