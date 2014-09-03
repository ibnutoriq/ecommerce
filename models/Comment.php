<?php
namespace models;

use libraries\Model as Model;

class Comment extends Model
{
	private $comments;
	const ITEM_PER_PAGE = 5;

	public function __construct()
	{

	}

	public function indexComment()
	{
		$sql = 'SELECT t1.*, t2.c_name FROM comments t1
			INNER JOIN customers t2 ON t2.customer_id = t1.customer_id';

		$this->comments = parent::connect()->prepare($sql);
		
		try {
			$this->comments->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->comments;
	}

	public function showComment($id)
	{
		$sql = 'SELECT t1.*, t2.c_name FROM comments t1
			INNER JOIN customers t2 ON t2.customer_id = t1.customer_id
			WHERE product_id = :product_id AND approve = :approve';

		$this->comments = parent::connect()->prepare($sql);
		$data = array(
				'approve' => 'Yes',
				'product_id' => $id
			);

		try {
			$this->comments->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->comments;
	}

	public function createComment()
	{
		$req = $this->app->request();
		
		$sql = 'INSERT INTO comments(customer_id, product_id, comment, approve)
						VALUES(:customer_id, :product_id, :comment, :approve)';

		$this->comments = parent::connect()->prepare($sql);
		$data = array(
				'approve' => 'No',
				'customer_id' => isset($_SESSION['idCustomer']) ? $_SESSION['idCustomer'] : null,
				'product_id' => $req->post('productID'),
				'comment' => $req->post('comment')
			);

		try {
			$this->comments->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}		
	}

	public function updateComment($id)
	{
		$req = $this->app->request();

		$sql = 'UPDATE comments SET approve = :approve WHERE comment_id = :id';

		$this->comments = parent::connect()->prepare($sql);
		$data = array(
				'approve' => $req->post('approve'),
				'id' => $id
			);

		try {
			$this->comments->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function deleteComment($id)
	{
		$sql = 'DELETE FROM comments WHERE comment_id = :id';

		$this->comments = parent::connect()->prepare($sql);
		$data = array(
				'id' => $id
			);

		try {
			$this->comments->execute($data);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function CommentPagination($id)
	{
		$page = $id != null ? $id : 1;
		$startFrom = self::ITEM_PER_PAGE * ($page - 1);

		$sql = "SELECT * FROM comments ORDER BY comment_id DESC LIMIT $startFrom, " . self::ITEM_PER_PAGE;

		$this->comments = parent::connect()->prepare($sql);

		try {
			$this->comments->execute();
		} catch(PDOException $e) {
			die($e->getMessage());
		}

		return $this->comments;
	}

	public function CommentPage($id)
	{
		$returnPage = array();
		$page = $id != null ? $id : 1;
		$amountPage = ceil(self::indexcomment()->rowCount()/self::ITEM_PER_PAGE);

		for($i = 1; $i <= $amountPage; $i++) {
			if($i == $id) {
				$returnPage[] = '<li class="active"><a href="admin/comments/page/'.$i.'">'.$i.'</a></li>';
			} else {
				$returnPage[] = '<li><a href="admin/comments/page/'.$i.'">'.$i.'</a></li>';
			}
		}

		return $returnPage;
	}
}
?>