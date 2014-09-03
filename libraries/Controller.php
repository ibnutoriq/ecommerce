<?php
namespace libraries;

class Controller
{
	private $app;

	public function __construct()
	{

	}

	public function routes()
	{
		$twigView = new \Slim\Views\Twig();
		$twigView->twigTemplateDirs = array(
				'./views/admin/',
				'./views/carousels/',
				'./views/carts/',
				'./views/checkout/',
				'./views/comments/',
				'./views/customers/',
				'./views/errors/',
				'./views/home/',
				'./views/layouts/',
				'./views/orders/',
				'./views/payment_confirmations/',
				'./views/products/',
				'./views/sales_report/',
				'./views/users/'
			); 

		$this->app = new \Slim\Slim(array(
				'debug' => true,
				'view' => $twigView
			));

		$this->app->add(new \Slim\Middleware\SessionCookie(array('secret' => 'caps123ee')));

		return $this->app;
	}

	public function redirectTo($name)
	{
		$url = $this->app->urlFor($name); 
		$this->app->redirect($url);
	}
}
?>