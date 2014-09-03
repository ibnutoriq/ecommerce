<?php
namespace controllers;

use libraries\Controller as Controller;
use models\User as User;
use libraries\Bcrypt as Bcrypt;

class AdminController extends Controller
{
	public function __construct()
	{

	}

	public function loginAdmin()
	{
		$flash = $this->app->view()->getData('flash');
		$errors = $tmpEmail = '';

		if(isset($flash['error'])) $errors = $flash['error'];
		if(isset($flash['tmpEmail'])) $tmpEmail = $flash['tmpEmail'];

		parent::routes()->render('login_admin.twig', array(
			'app_base' => $this->appBase,
			'errors' => $errors,
			'title' => 'Login',
			'tmp_email' => $tmpEmail
		));
	}

	public function index()
	{
		parent::routes()->render('index_admin.twig', array(
			'app_base' => $this->appBase, 
			'title' => 'Admin'
		));
	}

	public function loggedIn()
	{
		$req = $this->app->request();

		foreach(User::showUserByEmail($req->post('email')) as $row)
		{
			$stored_email = $row['u_email'];
			$stored_user_id = $row['user_id'];
			$stored_level = $row['level'];
			$stored_password = $row['u_password'];
		}

		if(User::countUser($req->post('email'))->fetchColumn() == 1 && Bcrypt::check($req->post('password'), $stored_password) === true) {
			$_SESSION['emailAdmin'] = $stored_email;
			$_SESSION['idAdmin'] = $stored_user_id;
			$_SESSION['levelAdmin'] = $stored_level;
			parent::redirectTo('indexAdmin');
		} else {
			$this->app->flash('error', "Email or Password doesn't match");
			$this->app->flash('tmpEmail', $req->post('email'));
			parent::redirectTo('loginAdmin');
		}
	}

	public function loggedOut()
	{
		unset($_SESSION['emailAdmin']);
		unset($_SESSION['levelAdmin']);
		
		parent::redirectTo('loginAdmin');
	}
}
?>