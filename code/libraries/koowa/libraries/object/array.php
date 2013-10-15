<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Array
 *
 * The KObjectArray class provides provides the main functionality of an array and at the same time implement the
 * features of KObject
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObjectArray extends KObject implements IteratorAggregate, ArrayAccess, Serializable, Countable
{
   /**
     * The data for each key in the array (key => value).
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor
     *
     * @param KObjectConfig $config  An optional KObjectConfig object with configuration options
     * @return KObjectArray
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_data = KObjectConfig::unbox($config->data);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'data' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Get a value by key
     *
     * @param   string  $key The key name.
     * @param   mixed   $default The default value
     * @throws  \InvalidArgumentException If the key cannot be found in the array
     * @return  string  The corresponding value.
     */
    public function get($key = null, $default = null)
    {
    	if ($key === null) {
    		throw new InvalidArgumentException('Empty key passed');
    	}

        $result = null;
    	if (isset($this->_data[$key])) {
    		$result = $this->_data[$key];
    	}

    	return $result;
    }
    
    /**
     * Set a value by key
     *
     * @param   string  $key   The key name
     * @param   mixed   $value The value for the key
     * @return  KObjectArray
     */
    public function set($key, $value = null)
    {
    	$this->_data[$key] = $value;
    	return $this;
    }
    
    /**
     * Test existence of a key
     *
     * @param  string  $key The key name
     * @return boolean
     */
    public function has($key)
    {
    	return array_key_exists($key, $this->_data);
    }
    
    /**
     * Unset a key
     *
     * @param   string  $key The key name
     * @return  KObjectArray
     */
    public function remove($key)
    {
    	unset($this->_data[$key]);
    	return $this;
    }
    
 	/**
     * Check if the offset exists
     *
     * Required by interface ArrayAccess
     *
     * @param   int   $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * Get an item from the array by offset
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset
     * @return  mixed   The item from the array
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Set an item in the array
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset
     * @param   mixed   $value
     * @return  KObjectArray
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->__set($offset, $value);
        }

        return $this;
    }

    /**
     * Unset an item in the array
     *
     * All numerical array keys will be modified to start counting from zero while literal keys won't be touched.
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset
     * @return  KObjectArray
     */
    public function offsetUnset($offset)
    {
    	$this->__unset($offset);
    	return $this;
    }

    /**
     * Get a new iterator
     *
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_data);
    }

 	/**
     * Serialize
     *
     * Required by interface Serializable
     *
     * @return  string
     */
    public function serialize()
    {
        return serialize($this->_data);
    }

    /**
     * Unserialize
     *
     * Required by interface Serializable
     *
     * @param   string  $data
     */
    public function unserialize($data)
    {
        $this->_data = unserialize($data);
    }

    /**
     * Returns the number of items
     *
     * Required by interface Countable
     *
     * @return int The number of items
     */
    public function count()
    {
    	return count($this->_data);
    }
    
    /**
     * Set the data from an array
     *
     * @param array $data An associative array of data
     * @return KObjectArray
     */
    public function fromArray(array $data)
    {
    	$this->_data = $data;
    	return $this;
    }
    
    /**
     * Return an associative array of the data
     *
     * @return array
     */
    public function toArray()
    {
    	return $this->_data;
    }
    
    /**
     * Get a value by key
     *
     * @param   string  $key The key name.
     * @return  string  The corresponding value.
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set a value by key
     *
     * @param   string  $key   The key name
     * @param   mixed   $value The value for the key
     * @return  void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Test existence of a key
     *
     * @param  string  $key The key name
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Unset a key
     *
     * @param   string  $key The key name
     * @return  void
     */
    public function __unset($key)
    {
        $this->remove($key);
    }
}