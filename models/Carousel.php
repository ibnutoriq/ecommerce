<?php
namespace models;

use libraries\Model as Model;

class Carousel extends Model
{
	private $carousels;
	const ITEM_PER_PAGE = 5;

	public function __construct()
	{

	}

	public function indexCarousel()
	{
		$sql = 'SELECT * FROM carousels';

		$this->carousels = parent::connect()->prepare($sql);

		try {
			$this->carousels->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->carousels;
	}

	public function showCarousel($id)
	{
		$sql = 'SELECT * FROM carousels WHERE carousel_id = :id';

		$this->carousels = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->carousels->execute($data);
			$results = $this->carousels->fetchAll();
			$this->carousels->closeCursor();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $results;
	}

	public function showImageCarousel($id)
	{
		$sql = 'SELECT image FROM carousels WHERE carousel_id = :id';

		$this->carousels = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->carousels->execute($data);
			$results = $this->carousels->fetchColumn();
			$this->carousels->closeCursor();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $results;
	}

	public function createCarousel()
	{
		$req = $this->app->request();
		$imageName = $_FILES['image']['name'];
		$imageTmp = $_FILES['image']['tmp_name'];
		$uniqueID = md5(uniqid(rand(), true));
		$fileType = strchr($imageName, '.');
		$newUpload = 'assets/img_public/' . $uniqueID . $fileType;

		move_uploaded_file($imageTmp, $newUpload);
		@chmod($newUpload, 0777);

		$sql = 'INSERT INTO carousels(image, title, description)
						VALUES(:image, :title, :description)';

		$this->carousels = parent::connect()->prepare($sql);
		$data = array(
				'image' => $newUpload,
				'title' => $req->post('title'),
				'description' => $req->post('description')
			);

		try {
			$this->carousels->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updateCarousel($id)
	{
		$req = $this->app->request();
		$imageName = $_FILES['image']['name'];
		$imageTmp = $_FILES['image']['tmp_name'];
		$uniqueID = md5(uniqid(rand(), true));
		$fileType = strchr($imageName, '.');
		$newUpload = 'assets/img_public/' . $uniqueID . $fileType;

		if($imageName != null) {
			unlink(carousel::showImagecarousel($id));
		}	

		move_uploaded_file($imageTmp, $newUpload);
		@chmod($newUpload, 0777);

		if($imageName != null) {
			$sql = 'UPDATE carousels SET image = :image, title = :title, description = :description where carousel_id = :id';
		} else {
			$sql = 'UPDATE carousels SET title = :title, description = :description where carousel_id = :id';
		}

		$this->carousels = parent::connect()->prepare($sql);
		if ($imageName != null) {
			$this->carousels->bindValue(':image', $newUpload);
		}
		$this->carousels->bindValue(':title', $req->post('title'));
		$this->carousels->bindValue(':description', $req->post('description'));
		$this->carousels->bindValue(':id', $id);

		try {
			$this->carousels->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deleteCarousel($id)
	{
		unlink(Carousel::showImageCarousel($id));	
		
		$sql = 'DELETE FROM carousels WHERE carousel_id = :id';

		$this->carousels = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->carousels->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function countCarousel()
	{
		return self::indexCarousel()->rowCount();
	}

	public function CarouselPagination($id)
	{
		$page = $id != null ? $id : 1;
		$startFrom = self::ITEM_PER_PAGE * ($page - 1);

		$sql = "SELECT * FROM carousels ORDER BY title LIMIT $startFrom, " . self::ITEM_PER_PAGE;

		$this->carousels = parent::connect()->prepare($sql);

		try {
			$this->carousels->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->carousels;
	}

	public function CarouselPage($id)
	{
		$returnPage = array();
		$page = $id != null ? $id : 1;
		$amountPage = ceil(self::indexCarousel()->rowCount()/self::ITEM_PER_PAGE);

		for($i = 1; $i <= $amountPage; $i++) {
			if($i == $id) {
				$returnPage[] = '<li class="active"><a href="admin/carousels/page/'.$i.'">'.$i.'</a></li>';
			} else {
				$returnPage[] = '<li><a href="admin/carousels/page/'.$i.'">'.$i.'</a></li>';
			}
		}

		return $returnPage;
	}
}
?>