<?php
/**
 * Opauth core
 *
 */
class Opauth{
/**
 * User configuraable settings
 * 
 * - Do not refer to this anywhere in logic, except in __construct() of Opauth
 * - TODO: Documentation on config
 */
	public $config;	
	
/**
 * Environment variables
 */
	public $env;
	
/** 
 * Defined strategies
 * - array key	: URL-friendly name, preferably all lowercase
 * - name		: Class and file name. If unset, Opauth automatically capitalize first letter as name
 * 
 * eg. array( 'facebook', 'flickr' );
 * eg. array( 'foobar' => array( 'name' => 'FooBar') );
 * 
 */
	public $strategies;
	
	public function __construct($config = array()){
		
		/* Setup */
		$this->config = array_merge(array(
			'uri' => $_SERVER['REQUEST_URI'],
			'path' => '/',
			'debug' => false
		), $config);
		
		$this->env = array(
			'LIB' => dirname(__FILE__).'/',
			'STRATEGY' => dirname(__FILE__).'/Strategy/',
			'debug' => $this->config['debug']
		);
		
		if (isset($this->config['strategies']) && is_array($this->config['strategies']) && count($this->config['strategies']) > 0)
			$this->strategies = $this->config['strategies'];
		else
			trigger_error('No Opauth strategies defined', E_USER_ERROR);
		
		/* Run */
		$this->_parseUri();
		
		if (!empty($this->env['strategy'])){
			if (array_search($this->env['strategy'], $this->strategies) !== false){
				require $this->env['LIB'].'OpauthStrategy.php';
				
				
			}
			else{
				trigger_error('Unsupported or undefined Opauth strategy - '.$this->env['strategy'], E_USER_ERROR);
			}
		}
	}
	
/**
 * Parses Request URI
 */
	protected function _parseUri(){
		$this->env['request'] = substr($this->config['uri'], strlen($this->config['path']) - 1);
		
		if (preg_match_all('/\/([A-Za-z0-9-_]+)/', $this->env['request'], $matches)){
			foreach ($matches[1] as $match){
				$this->env['params'][] = $match;
			}
		}
		
		if (!empty($this->env['params'][0])) $this->env['strategy'] = $this->env['params'][0];
	}
	
/**
 * Prints out variable with <pre> tags
 * - If debug is false, no printing
 */	
	protected function debug($var){
		if ($this->env['debug'] !== false){
			echo "<pre>";
			print_r($var);
			echo "</pre>";
		}
	}
}