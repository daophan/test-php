<?php

class UserController extends ControllerBase
{
	public function indexAction()
	{
		$this->view->t = $this->getTranslation();
	}
}