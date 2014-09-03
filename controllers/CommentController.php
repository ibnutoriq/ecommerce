<?php
namespace controllers;

use libraries\Controller as Controller;
use models\Comment as Comment;

class CommentController extends Controller
{
	public function __construct()
	{

	}

	public function index($id = null)
	{
		parent::routes()->render('index_comment.twig', array(
				'app_base' => $this->appBase,
				'comment_page' => Comment::CommentPage($id),
				'results' => Comment::CommentPagination($id),
				'title' => 'Comments'
			));
	}

	public function create()
	{
		Comment::createComment();
		$this->app->flash('infoComment', 'Your comment has been added, wait for verification from admin');
		$this->app->redirect($this->appBase . '/products/show/' . $this->app->request()->post('productID'));
	}

	public function update($id)
	{
		Comment::updateComment($id);
		parent::redirectTo('indexComment');
	}

	public function delete($id)
	{
		Comment::deleteComment($id);
		parent::redirectTo('indexComment');
	}
}
?>