<?php namespace Queiroz\WhmcsApi;

use Config;
use SimpleXMLElement;
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
		$params['url']          = $this->config->get('whmcs-api::url');
		$params['action']       = $action;

		// merge $actionParams with $params
		$params = array_merge($params, $actionParams);

		// call curl init connection
		return $this->curl($params);

	}

	public function curl($params)
	{
		// set url
		$url = $params['url'];
		// unset url
		unset($params['url']);

		$data = $this->curl->request($url, $params);

		// Identify XML result
		$xml = preg_match('/(\<\?xml)/', $data);
		
		if($xml) {
			return $this->formatXml($data);
		} else {
			return $this->formatObject($data);
		}

	}

	public function formatXml($input)
	{
		return new SimpleXMLElement($input);
	}

	public function formatObject($input)
	{

		$results = explode(';' ,$input);

		$object = new stdClass(); // standard object

		foreach($results as $result) {

			if(!empty($result)) {
				$resultValue = explode('=', $result);
				$object->$resultValue[0] = $resultValue[1];
			}

		}

		return $object;
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