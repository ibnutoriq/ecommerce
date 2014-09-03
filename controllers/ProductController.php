<?php
namespace controllers;

use libraries\Controller as Controller;
use models\Product as Product;
use models\Carousel as Carousel;
use models\Cart as Cart;
use models\Comment as Comment;
use models\Order as Order;

class ProductController extends Controller
{
	private static $errors;

	public function __construct()
	{

	}

	public function index($init, $id = null)
	{
		if($init ==  'admin') {
			parent::routes()->render('index_product.twig', array(
				'app_base' => $this->appBase,
				'product_page' => Product::ProductPage($id),
				'results' => Product::ProductPagination($id),
				'title' => 'Products'
			));
		} else {
			parent::routes()->render('index_home.twig', array(
				'app_base' => $this->appBase,
				'carousels' => Carousel::indexCarousel(),
				'carts' => Cart::countCart(),
				'is_carousel' => Carousel::countCarousel(),
				'is_customer' => isset($_SESSION['emailCustomer']) ? sizeof($_SESSION['emailCustomer']) : 0,
				'product_page' => Product::ProductPage($id, 'customer'),
				'results' => Product::ProductPagination($id),
				'title' => 'Home' 
			));
		}
	}

	public function show($id)
	{
		$flash = $this->app->view()->getData('flash');
		$infoComment = '';
		$infoCart = '';

		if(isset($flash['infoComment'])) {
			$infoComment = $flash['infoComment'];
		}

		if(isset($flash['infoCart'])) {
			$infoCart = $flash['infoCart'];
		}

		parent::routes()->render('show_product.twig', array(
				'app_base' => $this->appBase,
				'bugs1' => true, // Untuk mengatasi bugs twig pada if foo != 'bar2' or foo != 'bar1'
				'carts' => Cart::countCart(),
				'comments' => Comment::showComment($id),
				'info_comment' => $infoComment,
				'info_cart' => $infoCart,
				'is_customer' => isset($_SESSION['emailCustomer']) ? sizeof($_SESSION['emailCustomer']) : 0,
				'is_order' => Order::countOrder(),
				'results' => Product::showProduct($id),
				'related_product' => Product::showRelatedProduct(Product::showProductCategory($id), $id),
				'title' => Product::showProductName($id)
			));
	}

	public function add()
	{
		parent::routes()->render('new_product.twig', array(
			'app_base' => $this->appBase,
			'title' => 'New Product'
		));
	}

	public function create()
	{
		Product::createProduct();
		parent::redirectTo('indexProduct');
	}

	public function edit($id)
	{
		parent::routes()->render('edit_product.twig', array(
			'app_base' => $this->appBase,
			'products' => Product::showProduct($id),
			'title' => 'Edit Product'
		));
	}

	public function update($id)
	{
		Product::updateProduct($id);
		parent::redirectTo('indexProduct');
	}

	public function delete($id)
	{
		Product::deleteProduct($id);
		parent::redirectTo('indexProduct');
	}
}
?>