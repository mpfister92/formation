<?php
/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 10/10/2016
 * Time: 17:29
 */

namespace OCFram;


class Linking {
	private $app;
	private $module;
	private $action;
	private $vars = [];
	
	/** builds the url according to parameters
	 * @param string $app
	 * @param string $module
	 * @param string $action
	 * @param array $vars
	 *
	 * @return string $url
	 */
	public static function provideRoute($app, $module, $action, array $vars){
		$url = '';
		
		$xml = new \DOMDocument();
		$xml->load(__DIR__ . '/../../App/' . $app . '/Config/routes.xml');
		
		$routes = $xml->getElementsByTagName('route');
		
		
		
		foreach ($routes as $route){
			if($route->getAttribute('module') == $module && $route->getAttribute('action') == $action){
				$url .= $route->getAttribute('rewrite');
			}
		}
		
		if(null != $vars){
			foreach ($vars as $key => $value){
				$to_replace = "(". $key .")";
				str_replace($to_replace,$value,$url);
			}
		}
		
		return $url;
	}
}