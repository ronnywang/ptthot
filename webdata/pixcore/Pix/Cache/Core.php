<?php

/**
 * Pix_Cache_Core 
 * 
 * @package Pix_Cache
 * @version $id$
 * @copyright 2003-2009 PIXNET
 * @license 
 */
abstract class Pix_Cache_Core
{
    abstract public function __construct($config);
    abstract public function add($key, $value, $expire = null);
    abstract public function set($key, $value, $expire = null);
    abstract public function delete($key);
    abstract public function replace($key, $value, $expire = null);
    abstract public function inc($key);
    abstract public function dec($key);
    abstract public function get($key);
    public function load($key)
    {
	return $this->get($key);
    }
    public function save($key, $value, $options = array())
    {
	return $this->set($key, $value, $options);
    }
    public function remove($key)
    {
	return $this->delete($key);
    }
}
