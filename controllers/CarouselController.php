<?php
namespace controllers;

use libraries\Controller as Controller;
use models\Carousel as Carousel;

class CarouselController extends Controller
{
	public function __construct()
	{

	}

	public function index($id = null)
	{
		parent::routes()->render('index_carousel.twig', array(
				'app_base' => $this->appBase,
				'carousel_page' => Carousel::CarouselPage($id),
				'results' => Carousel::CarouselPagination($id),
				'title' => 'Carousels'
			));
	}

	public function add()
	{
		parent::routes()->render('new_carousel.twig', array(
				'app_base' => $this->appBase,
				'title' => 'New Carousel' 
			));
	}

	public function create()
	{
		Carousel::createCarousel();
		parent::redirectTo('indexCarousel');
	}

	public function edit($id)
	{
		parent::routes()->render('edit_carousel.twig', array(
			'app_base' => $this->appBase,
			'results' => Carousel::showCarousel($id),
			'title' => 'Edit Carousel'
		));
	}

	public function update($id)
	{
		Carousel::updateCarousel($id);
		parent::redirectTo('indexCarousel');
	}

	public function delete($id)
	{
		Carousel::deleteCarousel($id);
		parent::redirectTo('indexCarousel');
	}
}
?>