<?php

namespace Naga\Core\Model;

use Naga\Core\Database\Connection\CacheableDatabaseConnection;
use Naga\Core\nComponent;

/**
 * Abstract class for creating models.
 *
 * @package Naga\Core\Model
 * @author  BlindingLight <bloodredshade@gmail.com>
 */
abstract class Model extends nComponent
{
	/**
	 * @var array internal data storage
	 */
	private $_data = array();
	/**
	 * @var array data key -> database field key map
	 */
	protected $_fieldMap = array();

	public function __construct($id = null, CacheableDatabaseConnection $db, $load = true)
	{
		$this->_data['id'] = $id;
		$this->registerComponent('database', $db);
		if ($id && $load)
			$this->load();
	}

	public abstract function load();
	public abstract function save();
	public abstract function delete();
	public abstract function create();

	/**
	 * Gets the model's CacheableDatabaseConnection instance.
	 *
	 * @return CacheableDatabaseConnection
	 */
	public function db()
	{
		return $this->component('database');
	}

	/**
	 * Gets the model's id.
	 *
	 * @return null|string
	 */
	public function id()
	{
		return $this->_data['id'];
	}

	/**
	 * Sets properties from an array.
	 *
	 * @param array $properties
	 * @return $this
	 */
	public function mergeFrom(array $properties)
	{
		// filtering id
		if (isset($properties['id']))
			unset($properties['id']);

		$this->_data = array_merge($this->_data, $properties);

		return $this;
	}

	/**
	 * Gets a property.
	 *
	 * @param $property
	 * @return null|mixed
	 */
	public function __get($property)
	{
		if (isset($this->_fieldMap[$property]))
			$property = $this->_fieldMap[$property];

		return isset($this->_data[$property]) ? $this->_data[$property] : null;
	}

	/**
	 * Sets a property.
	 *
	 * @param $property
	 * @param $value
	 * @return $this
	 * @throws \Exception
	 */
	public function __set($property, $value)
	{
		if ($property == 'id' && $this->_data['id'] != 0)
			throw new \Exception("You can't change a model's id.");

		$this->_data[$property] = $value;

		return $this;
	}

	/**
	 * Tells whether a property exists.
	 *
	 * @param $property
	 * @return bool
	 */
	public function __isset($property)
	{
	  	return isset($this->_data[$property]) || isset($this->_fieldMap[$property]);
	}
}