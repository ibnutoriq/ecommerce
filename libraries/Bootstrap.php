<?php
namespace libraries;

use controllers\AdminController as AdminController;
use controllers\CarouselController as CarouselController;
use controllers\CartController as CartController;
use controllers\ChartController as ChartController;
use controllers\CheckoutController as CheckoutController;
use controllers\CommentController as CommentController;
use controllers\CustomerController as CustomerController;
use controllers\OrderController as OrderController;
use controllers\PaymentConfirmationController as PaymentConfirmationController;
use controllers\ProductController as ProductController;
use controllers\SalesReportController as SalesReportController;
use controllers\UserController as UserController;

class Bootstrap
{
	public $app;
	public $appBase;

	public function __construct()
	{
		$this->app = Controller::routes();
		$this->appBase = $this->app->request()->getRootURI();
		$app = $this->app->setName('app');

		$authAdmin = function($app) {
			return function($app) {
				if(!isset($_SESSION['emailAdmin'])) {
					$_SESSION['urlRedirect'] = $this->app->request()->getPathInfo();
					$this->app->flash('error', 'Login required');
          Controller::redirectTo('loginAdmin');
				}
			};
		};

		$authCustomer = function($app) {
			return function($app) {
				if(!isset($_SESSION['emailCustomer'])) {
					$_SESSION['urlRedirect'] = $this->app->request()->getPathInfo();
					$this->app->flash('error', 'Login required');
          Controller::redirectTo('loginCustomer');
				}
			};
		}; 

		$this->app->notFound(function() {
			$this->app->render('not_found.twig', array('app_base' => $this->appBase, 'title' => 'Error 404'));
		});

		// Untuk memeriksa ada atau tidaknya nilai (hanya ada pada proses development)
		$this->app->get('/dev', function() {
			$this->app->render('dev.twig', array(
					'var1' => isset($_SESSION['order_date']) ? sizeof($_SESSION['order_date']) : 'gak'
				));
		});

		$this->app->get('/', function() {
			ProductController::index('customer');
		})->name('indexHome');

		$this->app->get('/page/:id', function($id) {
			ProductController::index('customer', $id);
		});

		$this->app->get('/carts', $authCustomer($app), function() {
			CartController::index();
		})->name('cart');

		$this->app->post('/products/show/:id', $authCustomer($app), function() {
			CartController::create();
		});

		$this->app->post('/carts/:id', $authCustomer($app), function($id) {
			CartController::update($id);
		});

		$this->app->get('/carts/:id', $authCustomer($app), function($id) {
			CartController::delete($id);
		});

		$this->app->get('/checkout', $authCustomer($app), function() {
			CheckoutController::index();
		});

		$this->app->post('/checkout', $authCustomer($app), function() {
			CheckoutController::create();
		});

		$this->app->get('/products/show/:id', function($id) {
			ProductController::show($id);
		});

		$this->app->get('/login-admin', function() {
			if (isset($_SESSION['emailAdmin'])) {
				Controller::redirectTo('indexAdmin');
			} else {
				AdminController::loginAdmin();	
			}
		})->name('loginAdmin');

		$this->app->post('/login-admin', function() {
			AdminController::loggedIn();
		});

		$this->app->get('/logout-admin', function() {
			AdminController::loggedOut();
		});

		$this->app->get('/login-customer', function() {
			if (isset($_SESSION['emailCustomer'])) {
				Controller::redirectTo('indexHome');
			} else {
				CustomerController::loginCustomer();
			}
		})->name('loginCustomer');

		$this->app->post('/login-customer', function() {
			CustomerController::loggedIn();
		});

		$this->app->get('/logout-customer', function() {
			CustomerController::loggedOut();
		});

		$this->app->get('/customer-profile', $authCustomer($app), function() {
			CustomerController::showCustomerProfile(isset($_SESSION['emailCustomer']) ? $_SESSION['emailCustomer'] : null);
		})->name('indexCustomerProfile');

		$this->app->get('/customer-profile/edit', $authCustomer($app), function() {
			CustomerController::editCustomerProfile(isset($_SESSION['emailCustomer']) ? $_SESSION['emailCustomer'] : null);
		});

		$this->app->post('/customer-profile/edit', $authCustomer($app), function() {
			CustomerController::updateCustomerProfile(isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null);
		});

		$this->app->get('/admin', $authAdmin($app), function() {
			require_once 'CreateChart.php';
			AdminController::index();
		})->name('indexAdmin');

		$this->app->get('/admin/comments', $authAdmin($app), function() {
			CommentController::index();
		})->name('indexComment');

		$this->app->get('/admin/comments/page/:id', $authAdmin($app), function($id) {
			CommentController::index($id);
		});

		$this->app->post('/add-comment', $authCustomer($app), function() {
			CommentController::create();
		});

		$this->app->get('/admin/carousels', $authAdmin($app), function() {
			CarouselController::index();
		})->name('indexCarousel');

		$this->app->get('/admin/carousels/page/:id', $authAdmin($app), function($id) {
			CarouselController::index($id);
		});

		$this->app->get('/admin/carousels/new', $authAdmin($app), function() {
			CarouselController::add();
		});

		$this->app->post('/admin/carousels/new', $authAdmin($app), function() {
			CarouselController::create();
		});

		$this->app->get('/admin/carousels/edit/:id', $authAdmin($app), function($id) {
			CarouselController::edit($id);
		});

		$this->app->post('/admin/carousels/edit/:id', $authAdmin($app), function($id) {
			CarouselController::update($id);
		});

		$this->app->get('/admin/carousels/delete/:id', $authAdmin($app), function($id) {
			CarouselController::delete($id);
		});

		$this->app->post('/admin/comments/:id', $authAdmin($app), function($id) {
			CommentController::update($id);
		});

		$this->app->get('/admin/comments/delete/:id', $authAdmin($app), function($id) {
			CommentController::delete($id);
		});

		$this->app->get('/admin/customers', $authAdmin($app), function() {
			CustomerController::index();
		})->name('indexCustomer');

		$this->app->get('/admin/customers/page/:id', $authAdmin($app), function($id) {
			CustomerController::index($id);
		});

		$this->app->get('/admin/customers/new', $authAdmin($app), function() {
			CustomerController::add('admin');
		})->name('adminAddCustomer');

		$this->app->post('/admin/customers/new', $authAdmin($app), function() {
			CustomerController::create();
		});

		$this->app->get('/admin/customers/edit/:id', $authAdmin($app), function($id) {
			CustomerController::edit($id);
		})->name('editCustomer');
		
		$this->app->post('/admin/customers/edit/:id', $authAdmin($app), function($id) {
			CustomerController::update($id);
		});

		$this->app->get('/admin/customers/delete/:id', $authAdmin($app), function($id) {
			CustomerController::delete($id);
		});

		$this->app->get('/new-customer', function() {
			CustomerController::add('customer');
		})->name('addCustomer');

		$this->app->post('/new-customer', function() {
			CustomerController::create('customer');
		});

		$this->app->get('/order-status', $authCustomer($app), function() {
			OrderController::orderStatus();
		})->name('indexOrderStatus');

		$this->app->get('/order-status/show-order-details/:id', $authCustomer($app), function($id) {
			OrderController::showOrderDetail('customer', $id);
		});

		$this->app->post('/order-status/delete/:id', $authCustomer($app), function($id) {
			OrderController::deleteOrderStatus($id);
		});

		$this->app->get('/admin/orders', $authAdmin($app), function() {
			OrderController::index();
		})->name('indexOrder');

		$this->app->get('/admin/orders/page/:id', $authAdmin($app), function($id) {
			OrderController::index($id);
		});

		$this->app->get('/admin/orders/order_details/:id', $authAdmin($app), function($id) {
			OrderController::showOrderDetail('admin', $id);
		});

		$this->app->post('/admin/orders/:id', $authAdmin($app), function($id) {
			OrderController::update($id);
		});

		$this->app->get('/admin/orders/delete/:id', $authAdmin($app), function($id) {
			OrderController::delete($id);
		});

		$this->app->get('/payment-confirmation', $authCustomer($app), function() {
			PaymentConfirmationController::add();
		})->name('newPaymentConfirmation');

		$this->app->post('/payment-confirmation', $authCustomer($app), function() {
			PaymentConfirmationController::create();
		});

		$this->app->post('/admin/payment_confirmations/:id', $authAdmin($app), function($id) {
			PaymentConfirmationController::update($id);
		});

		$this->app->get('/admin/payment_confirmations/delete/:id', $authAdmin($app), function($id) {
			PaymentConfirmationController::delete($id);
		});

		$this->app->get('/admin/payment_confirmations', $authAdmin($app), function() {
			PaymentConfirmationController::index();
		})->name('indexPaymentConfirmation');

		$this->app->get('/admin/payment_confirmations/page/:id', $authAdmin($app), function($id) {
			PaymentConfirmationController::index($id);
		});

		$this->app->get('/admin/products', $authAdmin($app), function() {
			ProductController::index('admin');
		})->name('indexProduct');

		$this->app->get('/admin/products/page/:id', $authAdmin($app), function($id) {
			ProductController::index('admin', $id);
		});

		$this->app->get('/admin/products/new', $authAdmin($app), function() {
			ProductController::add();
		})->name('addProduct');

		$this->app->post('/admin/products/new', $authAdmin($app), function() {
			ProductController::create();
		});

		$this->app->get('/admin/products/edit/:id', $authAdmin($app), function($id) {
			ProductController::edit($id);
		})->name('editProduct');

		$this->app->post('/admin/products/edit/:id', $authAdmin($app), function($id) {
			ProductController::update($id);
		});

		$this->app->get('/admin/products/delete/:id', $authAdmin($app), function($id) {
			ProductController::delete($id);
		});

		$this->app->get('/admin/sales_report', function() {
			SalesReportController::index();
		});

		$this->app->get('/admin/sales_report/page/:id', $authAdmin($app), function($id) {
			SalesReportController::index($id);
		});

		$this->app->post('/admin/sales_report', $authAdmin($app), function() {
			SalesReportController::indexByDate();
		});

		$this->app->get('/admin/users', $authAdmin($app), function() {
			UserController::index();
		})->name('indexUser');

		$this->app->get('/admin/users/page/:id', $authAdmin($app), function($id) {
			UserController::index($id);
		});

		$this->app->get('/admin/users/new', function() {
			UserController::add();
		})->name('addUser');

		$this->app->post('/admin/users/new', function() {
			UserController::create();
		});

		$this->app->get('/admin/users/edit/:id', $authAdmin($app), function($id) {
			UserController::edit($id);
		})->name('editUser');

		$this->app->post('/admin/users/edit/:id', $authAdmin($app), function($id) {
			UserController::update($id);
		});

		$this->app->get('/admin/users/delete/:id', $authAdmin($app), function($id) {
			UserController::delete($id);
		});

		$this->app->run();
	}
}
?>