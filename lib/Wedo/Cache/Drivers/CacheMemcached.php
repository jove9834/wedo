<?php
/**
 * Memcached Cache
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Cache\Drivers;

use Wedo\Config;
use Wedo\Cache\CacheInterface;

/**
 * Memcached Cache
 */
class CacheMemcached implements CacheInterface {

	/**
	 * Holds the memcached object
	 *
	 * @var object
	 */
	protected $_memcached;

	/**
	 * Memcached configuration
	 *
	 * @var array
	 */
	protected $_memcache_conf	= array(
		'default' => array(
			'host'		=> '127.0.0.1',
			'port'		=> 11211,
			'weight'	=> 1
		)
	);

	/**
	 * Fetch from cache
	 *
	 * @param	string	$id	Cache ID
	 * @return	mixed	Data on success, FALSE on failure
	 */
	public function get($id)
	{
		$data = $this->_memcached->get($id);

		return is_array($data) ? $data[0] : $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Save
	 *
	 * @param	string	$id	Cache ID
	 * @param	mixed	$data	Data being cached
	 * @param	int	$ttl	Time to live
	 * @param	bool	$raw	Whether to store the raw value
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function get($id, $data, $ttl = 60, $raw = FALSE)
	{
		if ($raw !== TRUE)
		{
			$data = array($data, time(), $ttl);
		}

		if (get_class($this->_memcached) === 'Memcached')
		{
			return $this->_memcached->set($id, $data, $ttl);
		}
		elseif (get_class($this->_memcached) === 'Memcache')
		{
			return $this->_memcached->set($id, $data, 0, $ttl);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param	mixed	key to be deleted.
	 * @return	bool	true on success, false on failure
	 */
	public function delete($id)
	{
		return $this->_memcached->delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to add
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function increment($id, $offset = 1)
	{
		return $this->_memcached->increment($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to reduce by
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function decrement($id, $offset = 1)
	{
		return $this->_memcached->decrement($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the Cache
	 *
	 * @return	bool	false on failure/true on success
	 */
	public function clean()
	{
		return $this->_memcached->flush();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @return	mixed	array on success, false on failure
	 */
	public function cacheInfo()
	{
		return $this->_memcached->getStats();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param	mixed	key to get cache metadata on
	 * @return	mixed	FALSE on failure, array on success.
	 */
	public function getMetadata($id)
	{
		$stored = $this->_memcached->get($id);

		if (count($stored) !== 3)
		{
			return FALSE;
		}

		list($data, $time, $ttl) = $stored;

		return array(
			'expire'	=> $time + $ttl,
			'mtime'		=> $time,
			'data'		=> $data
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Setup memcached.
	 *
	 * @return	bool
	 */
	protected function _setup_memcached()
	{
		$config = Config::load('memcached');
		if (is_array($config))
		{
			$defaults = $this->_memcache_conf['default'];
			$this->_memcache_conf = array();

			foreach ($config['memcached'] as $name => $conf)
			{
				$this->_memcache_conf[$name] = $conf;
			}
		}

		if (class_exists('Memcached', FALSE))
		{
			$this->_memcached = new Memcached();
		}
		elseif (class_exists('Memcache', FALSE))
		{
			$this->_memcached = new Memcache();
		}
		else
		{
			Logger::error('Failed to create object for Memcached Cache; extension not loaded?');
			return FALSE;
		}

		foreach ($this->_memcache_conf as $cache_server)
		{
			isset($cache_server['hostname']) OR $cache_server['hostname'] = $defaults['host'];
			isset($cache_server['port']) OR $cache_server['port'] = $defaults['host'];
			isset($cache_server['weight']) OR $cache_server['weight'] = $defaults['weight'];

			if (get_class($this->_memcached) === 'Memcache')
			{
				// Third parameter is persistance and defaults to TRUE.
				$this->_memcached->addServer(
					$cache_server['hostname'],
					$cache_server['port'],
					TRUE,
					$cache_server['weight']
				);
			}
			else
			{
				$this->_memcached->addServer(
					$cache_server['hostname'],
					$cache_server['port'],
					$cache_server['weight']
				);
			}
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Is supported
	 *
	 * Returns FALSE if memcached is not supported on the system.
	 * If it is, we setup the memcached object & return TRUE
	 *
	 * @return	bool
	 */
	public function isSupported()
	{
		if (! extension_loaded('memcached') && ! extension_loaded('memcache'))
		{
			Logger::debug('The Memcached Extension must be loaded to use Memcached Cache.');
			return FALSE;
		}

		return $this->_setup_memcached();
	}

}

/* End of file Cache_memcached.php */