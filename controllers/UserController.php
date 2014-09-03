<?php
namespace controllers;

use libraries\Controller as Controller;
use models\User as User;

class UserController extends Controller
{
	private static $errors;
	private static $tmpEmail;
	private static $url;

	public function __construct()
	{
		
	}

	public function index($id = null)
	{
		parent::routes()->render('index_user.twig', array(
			'app_base' => $this->appBase,
			'count_user' => User::indexUser()->rowCount(),
			'user_page' => User::userPage($id),
			'results' => User::userPagination($id),
			'title' => 'Users'
		));
	}

	public function add()
	{	
		self::displayFlash($this->app->view()->getData('flash'));
		parent::routes()->render('new_user.twig', array(
			'app_base' => $this->appBase,
			'errors' => self::$errors,
			'title' => 'New User',
			'tmp_email' => self::$tmpEmail
		));
	}

	public function create()
	{
		self::flashAny('add');
		User::createUser();
		parent::redirectTo('indexUser');
	}

	public function edit($id)
	{
		self::displayFlash($this->app->view()->getData('flash'));
		parent::routes()->render('edit_user.twig', array(
			'app_base' => $this->appBase,
			'errors' => self::$errors,
			'results' => User::showUser($id), 
			'title' => 'Edit User',
			'tmp_email' => self::$tmpEmail
		));
	}

	public function update($id)
	{
		self::flashAny('edit', $id);
		User::updateUser($id);
		parent::redirectTo('indexUser');
	}

	public function delete($id)
	{
		User::deleteUser($id);
		parent::redirectTo('indexUser');
	}

	public function flashAny($init, $id = null)
	{
		$req = $this->app->request();
		self::$errors  = array();

		$idData = explode('/', $_SERVER['REQUEST_URI']);
		$idAny  = end($idData);

		if(filter_var($idAny, FILTER_VALIDATE_INT) === false) {
			if(User::countUser($req->post('email'))->fetchColumn() == 1) self::$errors[] = 'Email sudah terdaftar';
		}

    if(count(self::$errors) > 0) {
    	$this->app->flash('errors', self::$errors);
    	$this->app->flash('tmpEmail', self::$tmpEmail);
    	if($init == 'add') {
    		parent::redirectTo('addUser');
    	} else {
    		self::$url = $this->app->urlFor('editUser', array('id' => $id));
				$this->app->redirect(self::$url);
    	}
    }
  }

  public function displayFlash($flash)
  {
  	self::$errors = self::$tmpEmail = '';

  	if(isset($flash['errors'])) self::$errors = $flash['errors'];
  	if(isset($flash['tmpEmail'])) self::$tmpEmail = $flash['tmpEmail'];
  }
}
?>