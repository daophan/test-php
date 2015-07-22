<?php

class IndexController extends ControllerBase
{
	public function indexAction()
	{
		if($this->session->has('user-id') && User::count("ID = '".$this->session->get('user-id')."'"))
		{
			return $this->response->redirect('user');
		}
		
		$this->view->t = $this->getTranslation();

		// Check if request has made with POST
		if ($this->request->isPost()) {

            // Access POST data
			$username = $this->request->getPost("username");
			$password = $this->request->getPost("password");

			$user = User::findFirst("UserName = '$username'");

			if($user && $this->security->checkHash($password, $user->PassWord)) // Login
			{
				$this->session->set("user-id", $user->ID);
				$this->session->set("user-role", $user->RoleID);

				return $this->response->redirect('user');
			}
			else $this->view->error = true;
		}

	}
}