<?php

use Phalcon\Translate\Adapter\NativeArray;

class IndexController extends ControllerBase
{
	public function indexAction()
	{
		$this->view->t = $this->getTranslation();
		$this->view->c = $this->cookies;

		// Check if request has made with POST
		if ($this->request->isPost()) {

            // Access POST data
			$username = $this->request->getPost("username");
			$password = $this->request->getPost("password");

			$user = User::findFirst("UserName = '$username'");

			if($user && $this->security->checkHash($password, $user->PassWord)) // Login
			{
				return $this->dispatcher->forward(array(
					'controller' => 'user',
				    'action' => 'profile'
				));
			}
			else $this->view->error = true;
		}

	}
}