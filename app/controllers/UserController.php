<?php

class UserController extends ControllerBase
{
	public function indexAction()
	{
        $uid = $this->session->get('user-id');
        $user = User::find($uid)->getFirst();

        if(!$uid || !$user)
        {
            return $this->response->redirect('index');
        }

		$this->view->t = $this->getTranslation();
        $this->view->images = $user->Image;
	}
}