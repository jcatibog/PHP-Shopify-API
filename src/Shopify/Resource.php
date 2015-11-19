<?php

namespace Shopify;

abstract class Resource
{
    protected $config = array(
        'client' => null,
        'url'    => ''
    );

    public function __construct($config = null)
    {
        if ($config !== null) {
            $this->setConfig($config);
        }
    }

    public function setConfig($config = array())
    {
        if ($config instanceof \Zend\Config\Config) {
            $config = $config->toArray();
        } elseif (!is_array($config)) {
            throw new \InvalidArgumentException('Array or Zend_Config object expected, got '. gettype($config));
        }

        foreach ($config as $k => $v) {
            $this->_config[strtolower($k)] = $v;
        }

        return $this;
    }

    protected function getClient()
    {
        if (!$this->config['client'] instanceof Client) {
            throw new \Exception('Shopify\Client object expected, got '. gettype($this->_config['client']));
        }

        return $this->config['client'];
    }

    protected function getUrl()
    {
        if ($this->config['url'] == '') {
            throw new \Exception('Url is empty.');
        }

        return $this->config['url'];
    }

    protected function flattenOptions($options = null)
    {
        if (!is_array($options)) {
            throw new \InvalidArgumentException('Array expected, got '. gettype($options));
        }

        $flatOptions = array();
        foreach ($options as $option => $value) {
            if (is_array($value)) {
                $value = implode(',', array_map('trim', $value));
            }
            $flatOptions[] = $option .'='. $value;
        }

        return implode('&', $flatOptions);
    }

    abstract public function get($options = null);
}
