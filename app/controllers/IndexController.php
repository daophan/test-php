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
		else if($this->cookies->has('username') && $this->cookies->has('password'))
		{
			$username = $this->cookies->get("username");
			$password = $this->cookies->get("password");

			$user = User::findFirst("UserName = '$username'");
			if($user && $this->security->checkHash($password, $user->PassWord))
			{
				$this->session->set("user-id", $user->ID);
				$this->session->set("user-role", $user->RoleID);
				
				return $this->response->redirect('user');					
			}
		}


		$this->view->t = $this->getTranslation();

		// Check if request has made with POST
		if ($this->request->isPost()) {

            // Access POST data
			$username = $this->request->getPost("username");
			$password = $this->request->getPost("password");
			$remember = $this->request->getPost("remember");

			$user = User::findFirst("UserName = '$username'");

			if($user && $this->security->checkHash($password, $user->PassWord)) // Login
			{
				$this->session->set("user-id", $user->ID);
				$this->session->set("user-role", $user->RoleID);

				if($remember)
				{
					$this->cookies->set("cookname", $username, time()+2592000, "/");
					$this->cookies->set("cookpass", $password, time()+2592000, "/");
				}

				return $this->response->redirect('user');
			}
			else $this->view->error = true;
		}
	}
}