<?php
namespace models;

use libraries\Model as Model;
use libraries\Bcrypt as Bcrypt;

class User extends Model
{
	private $users;
	const ITEM_PER_PAGE = 5;

	public function __construct()
	{

	}

	public function indexUser()
	{
		$sql = 'SELECT * FROM users';

		$this->users = parent::connect()->prepare($sql);

		try {
			$this->users->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->users;
	}

	public function showUser($id)
	{
		$sql = 'SELECT * FROM users WHERE user_id = :id';

		$this->users = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->users->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage);
		}

		return $this->users;
	}

	public function createUser()
	{
		$req = $this->app->request();
		$imageName = $_FILES['image']['name'];
		$imageTmp = $_FILES['image']['tmp_name'];
		$uniqueID = md5(uniqid(rand(), true));
		$fileType = strchr($imageName, '.');
		$newUpload = 'assets/img_public/' . $uniqueID . $fileType;

		move_uploaded_file($imageTmp, $newUpload);
		@chmod($newUpload, 0777);

		$sql = 'INSERT INTO users(u_email, u_password, u_image, level) 
						VALUES(:u_email, :u_password, :u_image, :level)';

		

		$this->users = parent::connect()->prepare($sql);
		$data = array(
				'u_email' => $req->post('email'),
				'u_password' => Bcrypt::hash($req->post('password')),
				'u_image' => $newUpload,
				'level' => $req->post('level')
			);

		try {
			$this->users->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function updateUser($id)
	{
		$req = $this->app->request();
		$imageName = $_FILES['image']['name'];
		$imageTmp = $_FILES['image']['tmp_name'];
		$uniqueID = md5(uniqid(rand(), true));
		$fileType = strchr($imageName, '.');
		$newUpload = 'assets/img_public/' . $uniqueID . $fileType;

		if($imageName != null) {
			unlink(User::showImageUser($id));
		}	

		move_uploaded_file($imageTmp, $newUpload);
		@chmod($newUpload, 0777);

		if($imageName != null) {
			$sql = 'UPDATE users SET u_email = :u_email, u_password = :u_password, u_image = :u_image, level = :level WHERE user_id = :id';
		} else {
			$sql = 'UPDATE users SET u_email = :u_email, u_password = :u_password, level = :level WHERE user_id = :id';
		}
		
		$this->users = parent::connect()->prepare($sql);

		$this->users->bindValue(':u_email', $req->post('email'));
		$this->users->bindValue(':u_password', Bcrypt::hash($req->post('password')));
		if($imageName != null) {
			$this->users->bindValue(':u_image', $newUpload);
		}
		$this->users->bindValue(':level', $req->post('level'));
		$this->users->bindValue(':id', $id);

		try {
			$this->users->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deleteUser($id)
	{
		unlink(User::showImageUser($id));	

		$sql = 'DELETE FROM users WHERE user_id = :id';

		$this->users = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->users->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function countUser($email) 
	{
		$sql = 'SELECT COUNT(*) FROM users WHERE u_email = :u_email';
		$data = array('u_email' => $email);
		$this->users = parent::connect()->prepare($sql);

		try {
			$this->users->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->users;
	}

	public function showUserByEmail($email)
	{
		$sql = 'SELECT * FROM users WHERE u_email = :u_email';

		$this->users = parent::connect()->prepare($sql);
		$data = array('u_email' => $email);

		try {
			$this->users->execute($data);
			$results = $this->users->fetchAll();
			$this->users->closeCursor();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $results;
	}

	public function showImageUser($id)
	{
		$sql = 'SELECT image FROM users WHERE user_id = :id';

		$this->products = parent::connect()->prepare($sql);
		$data = array('id' => $id);

		try {
			$this->products->execute($data);
			$results = $this->products->fetchColumn();
			$this->products->closeCursor();
		} catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $results;
	}

	public function UserPagination($id)
	{
		$page = $id != null ? $id : 1;
		$startFrom = self::ITEM_PER_PAGE * ($page - 1);

		$sql = "SELECT * FROM users ORDER BY user_id LIMIT $startFrom, " . self::ITEM_PER_PAGE;

		$this->users = parent::connect()->prepare($sql);

		try {
			$this->users->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->users;
	}

	public function UserPage($id)
	{
		$returnPage = array();
		$page = $id != null ? $id : 1;
		$amountPage = ceil(self::indexUser()->rowCount()/self::ITEM_PER_PAGE);

		for($i = 1; $i <= $amountPage; $i++) {
			if($i == $id) {
				$returnPage[] = '<li class="active"><a href="admin/users/page/'.$i.'">'.$i.'</a></li>';
			} else {
				$returnPage[] = '<li><a href="admin/users/page/'.$i.'">'.$i.'</a></li>';
			}
		}

		return $returnPage;
	}
} 
?>