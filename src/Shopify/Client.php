<?php

namespace Shopify;

use Zend\Http\Request;

class Client
{
    const USER_AGENT = 'PHP Shopify API v0.0.1';

    private $client = null;
    private $request = null;

    private $config = array(
        'api_key' => '',
        'secret'  => '',
        'shop'    => '',
        'token'   => ''
    );


    public function __construct($config = null)
    {
        if ($config !== null) {
            $this->setConfig($config);
        }

        $this->request = new \Zend\Http\Request();
    }

    public function request($request = null)
    {
        if (is_null($request)) {
            throw new \InvalidArgumentException('You must specify a request url.');
        } elseif (!is_string($request) || !($request instanceof \Zend\Http\Request)) {
            throw new \InvalidArgumentException('String or \\Zend\\Http\\Request object expected, got ' . gettype($request));
        }

        $this->_getClient()->setUri($this->_getUrl() . $request);

        return json_decode($this->_getClient()->getResponse()->getBody());
    }

    private function getClient()
    {
        if (!is_object($this->client) || !($this->client instanceof \Zend\Http\Client)) {
            $this->_client = new \Zend\Http\Client();
        }

        return $this->_client;
    }

    private function getUrl()
    {
        if (!isset($this->config['api_key'])
            || $this->config['api_key'] == ''
        ) {
            throw new \Exception('Api key not set!');
        }

        if (!isset($this->config['secret'])
            || $this->config['secret'] == ''
        ) {
            throw new \Exception('Secret not set!');
        }

        if (!isset($this->config['shop'])
            || $this->config['shop'] == ''
        ) {
            throw new \Exception('Shop not set!');
        }

        return 'https://'. $this->config['api_key'] .':'. md5($this->config['secret'] . $this->config['token']) .'@'. $this->config['shop'];
    }

    public function setConfig($config = array())
    {
        if ($config instanceof \Zend\Config\Config) {
            $config = $config->toArray();

        } elseif (!is_array($config)) {
            throw new \InvalidArgumentException('Array or Zend_Config object expected, got ' . gettype($config));
        }

        foreach ($config as $k => $v) {
            $this->config[strtolower($k)] = $v;
        }

        return $this;
    }
}
