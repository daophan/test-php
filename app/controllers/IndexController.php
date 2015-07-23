<?php

class IndexController extends ControllerBase
{
	public function indexAction()
	{
        $uid = $this->session->get('user-id');

        if($uid && User::find($uid)->count() > 0)
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