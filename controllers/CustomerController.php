<?php
namespace controllers;

use libraries\Controller as Controller;
use models\Cart as Cart;
use models\Customer as Customer;
use libraries\Bcrypt as Bcrypt;

class CustomerController extends Controller
{
	private static $errors;
	private static $tmpAddress;
	private static $tmpEmail;
	private static $tmpName;
	private static $tmpNotes;
	private static $tmpPhone;
	private static $url;

	public function __construct()
	{

	}

	public function index($id = null)
	{
		parent::routes()->render('index_customer.twig', array(
			'app_base' => $this->appBase,
			'customer_page' => customer::CustomerPage($id),
			'results' => customer::CustomerPagination($id),
			'title' => 'Customers'
		));
	}

	public function add($init)
	{
		self::displayFlash($this->app->view()->getData('flash'));
		parent::routes()->render($init == 'admin' ? 'admin_new_customer.twig' : 'new_customer.twig', array(
			'app_base' => $this->appBase,
			'errors' => self::$errors,
			'title' => 'New Customer',
			'tmp_address' => self::$tmpAddress,
			'tmp_email' => self::$tmpEmail, 
			'tmp_name' => self::$tmpName, 
			'tmp_notes' => self::$tmpNotes,
			'tmp_phone' => self::$tmpPhone
		));
	}

	public function create($init)
	{
		if($init == 'Admin') {
			self::flashAny('adminAdd');
			Customer::createCustomer();
			parent::redirectTo('indexCustomer');
		} else {
			self::flashAny('add');
			Customer::createCustomer();
			parent::redirectTo('indexHome');
		}
	}

	public function edit($id)
	{
		self::displayFlash($this->app->view()->getData('flash'));
		parent::routes()->render('edit_customer.twig', array(
			'app_base' => $this->appBase,
			'errors' => self::$errors,
			'results' => Customer::showCustomer($id),
			'title' => 'Edit Customer',
			'tmp_address' => self::$tmpAddress, 
			'tmp_email' => self::$tmpEmail, 
			'tmp_name' => self::$tmpName,
			'tmp_notes' => self::$tmpNotes,
			'tmp_phone' => self::$tmpPhone
		));
	}

	public function update($id)
	{
		self::flashAny('edit', $id);
		Customer::updateCustomer($id);
		parent::redirectTo('indexCustomer');
	}

	public function delete($id)
	{
		Customer::deleteCustomer($id);
		parent::redirectTo('indexCustomer');
	}

	public function showCustomerProfile($email)
	{
		parent::routes()->render('profile_customer.twig', array(
				'app_base' => $this->appBase,
				'carts' => Cart::countCart(),
				'is_customer' => isset($_SESSION['emailCustomer']) ? sizeof($_SESSION['emailCustomer']) : 0,
				'results' => Customer::showCustomerByEmail($email),
				'title' => 'Profile'
			));
	}

	public function editCustomerProfile($email)
	{
		parent::routes()->render('edit_profile_customer.twig', array(
				'app_base' => $this->appBase,
				'carts' => Cart::countCart(),
				'is_customer' => isset($_SESSION['emailCustomer']) ? sizeof($_SESSION['emailCustomer']) : 0,
				'results' => Customer::showCustomerByEmail($email),
				'title' => 'Edit Profile'
			));
	}

	public function updateCustomerProfile($id)
	{
		Customer::updateCustomerProfile($id);
		parent::redirectTo('indexCustomerProfile');	
	}

	public function loginCustomer()
	{
		$flash = $this->app->view()->getData('flash');
		$errors = $tmpEmail = '';

		if(isset($flash['error'])) $errors = $flash['error'];
		if(isset($flash['tmpEmail'])) $tmpEmail = $flash['tmpEmail'];

		parent::routes()->render('login_customer.twig', array(
			'app_base' => $this->appBase,
			'errors' => $errors,
			'title' => 'Login Customer',
			'tmp_email' => $tmpEmail
		));
	}

	public function loggedIn()
	{
		$req = $this->app->request();

		foreach(Customer::showCustomerByEmail($req->post('email')) as $row)
		{
			$stored_id = $row['customer_id'];
			$stored_email = $row['c_email'];
			$stored_name = $row['c_name'];
			$stored_password = $row['c_password'];
		}

		if(Customer::countCustomer($req->post('email'))->fetchColumn() == 1 && Bcrypt::check($req->post('password'), $stored_password) === true) {
			$_SESSION['idCustomer'] = $stored_id;
			$_SESSION['emailCustomer'] = $stored_email;
			$_SESSION['nameCustomer'] = $stored_name;
			$_SESSION['passwordCustomer'] = $stored_password;
			parent::redirectTo('indexHome');
		} else {
			$this->app->flash('error', "Email or Password doesn't match");
			$this->app->flash('tmpEmail', $req->post('email'));
			parent::redirectTo('loginCustomer');
		}
	}

	public function loggedOut()
	{
		unset($_SESSION['idCustomer']);
		unset($_SESSION['emailCustomer']);
		unset($_SESSION['nameCustomer']);
		unset($_SESSION['passwordCustomer']);

		Controller::redirectTo('indexHome');
	}

	public function flashAny($init, $id = null)
	{
		$req = $this->app->request();
		self::$errors  = array();

		$idData = explode('/', $_SERVER['REQUEST_URI']);
		$idAny = end($idData);

		if(filter_var($idAny, FILTER_VALIDATE_INT) === false) {
			if(Customer::countCustomer($req->post('email'))->fetchColumn() == 1) self::$errors[] = 'That email already exists.';
		}

		if(count(self::$errors) > 0) {
			$this->app->flash('errors', self::$errors);
			$this->app->flash('tmpAddress', $req->post('address'));
			$this->app->flash('tmpEmail', $req->post('email'));
			$this->app->flash('tmpName', $req->post('name'));
			$this->app->flash('tmpNotes', $req->post('notes'));
			$this->app->flash('tmpPhone', $req->post('phone'));

			if($init == 'add') {
				parent::redirectTo('addCustomer');
			} elseif($init == 'adminAdd') {
				parent::redirectTo('adminAddCustomer');
			} else {
				self::$url = $this->app->urlFor('editCustomer', array('id' => $id));
				$this->app->redirect(self::$url); 
			}
		}
	}

	public function displayFlash($flash)
	{
		self::$errors = self::$tmpAddress = self::$tmpEmail = self::$tmpName = self::$tmpNotes = self::$tmpPhone = '';

		if(isset($flash['errors'])) self::$errors = $flash['errors'];
		if(isset($flash['tmpAddress'])) self::$tmpAddress = $flash['tmpAddress'];
		if(isset($flash['tmpEmail'])) self::$tmpEmail = $flash['tmpEmail'];
		if(isset($flash['tmpName'])) self::$tmpName = $flash['tmpName'];
		if(isset($flash['tmpNotes'])) self::$tmpNotes = $flash['tmpNotes'];
		if(isset($flash['tmpPhone'])) self::$tmpPhone = $flash['tmpPhone'];
	}
}
?>