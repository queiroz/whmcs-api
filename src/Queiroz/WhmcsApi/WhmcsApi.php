<?php namespace Queiroz\WhmcsApi;

use Config;
use stdClass;
use Illuminate\Config\Repository;

class WhmcsApi 
{
	protected $curl;
	protected $config;

	public function __construct(Repository $config, WhmcsCurl $curl)
	{
		$this->curl = $curl;
		$this->config = $config;
	}

	public function init($action, $actionParams)
	{

		$params = array();
		$params['username']     = $this->config->get('whmcs-api::username');
		$params['password']     = md5($this->config->get('whmcs-api::password'));
		$params['action']       = $action;
		$params["responsetype"] = "json";

		// merge $actionParams with $params
		$params = array_merge($actionParams, $params);

		// call curl init connection
		return $this->curl($params);

	}

	public function curl($params)
	{
		$data = $this->curl->request($this->config->get('whmcs-api::url'), $params);
		
		return json_decode($data);
	}

	public function execute($action, $params)
	{
		return $this->init($action, $params);
	}

	public function __call($method, $params)
	{
		return $this->execute($action, $params[0]);
	}

}
