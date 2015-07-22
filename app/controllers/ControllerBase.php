<?php

use Phalcon\Mvc\Controller;
use Phalcon\Translate\Adapter\NativeArray;

class ControllerBase extends Controller
{
	protected function getTranslation()
	{
	    //Ask browser what is the best language
	    //$language = $this->request->getBestLanguage();
		if ($this->cookies->has('lang')) {
			$language = $this->cookies->get('lang')->getValue();
			if (file_exists(dirname(__DIR__)."/messages/" . $language . ".php")) {
				require dirname(__DIR__)."/messages/" . $language . ".php";
			}
			else
				require dirname(__DIR__)."/messages/en.php";
		}
		else
			require dirname(__DIR__)."/messages/vi.php";

    	//Return a translation object
		return new NativeArray(array(
			"content" => $messages
			));
	}
}