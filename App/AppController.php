<?php

use \Detection\MobileDetect;

trait AppController {
	public function run(){
		require_once 'C:\Users\mpfister\Desktop\UwAmp\www\formation\vendor\mobiledetect\mobiledetectlib\namespaced\Detection\MobileDetect.php';
		$detect = new MobileDetect;
		$device_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablette' : 'téléphone') : 'ordinateur');
		$method = 'page()->addVar(\'device_type\',$device_type)';
		$this->$method();
		
		//$user = self::_app->user();
		//_page->addVar('user',$user);
	}
}


