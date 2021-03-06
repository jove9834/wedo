<?php
/**
 * Redis Cache
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Cache\Drivers;

use Wedo\Config;
use Wedo\Logger;
use Wedo\Cache\CacheInterface;

/**
 * Redis Cache
 */ 
class CacheRedis implements CacheInterface {
	/**
	 * Default config
	 *
	 * @static
	 * @var	array
	 */
	protected static $_default_config = array(
		'socket_type' => 'tcp',
		'host' => '127.0.0.1',
		'password' => NULL,
		'port' => 6379,
		'database' => 0,
		'timeout' => 0
	);

	/**
	 * Redis connection
	 *
	 * @var	Redis
	 */
	protected $_redis;
	
	// ------------------------------------------------------------------------

	/**
	 * Get cache
	 *
	 * @param	string	Cache key
	 * @return	mixed
	 */
	public function get($key)
	{
		return $this->_redis->get($key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Save cache
	 *
	 * @param	string	$key	Cache ID
	 * @param	mixed	$data	Data to save
	 * @param	int	$ttl	Time to live in seconds
	 * @param	bool	$raw	Whether to store the raw value (unused)
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function set($key, $data, $ttl = 0, $raw = FALSE)
	{
		return ($ttl)
			? $this->_redis->setex($key, $ttl, $data)
			: $this->_redis->set($key, $data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from cache
	 *
	 * @param	string	Cache key
	 * @return	bool
	 */
	public function delete($key)
	{
		$keys = $this->_redis->keys($key);
		return $this->_redis->delete($keys) > 0;
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
		return $this->_redis->exists($id)
			? $this->_redis->incr($id, $offset)
			: FALSE;
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
		return $this->_redis->exists($id)
			? $this->_redis->decr($id, $offset)
			: FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean cache
	 *
	 * @return	bool
	 * @see		Redis::flushDB()
	 */
	public function clean()
	{
		return $this->_redis->flushDB();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get cache driver info
	 *
	 * @param	string	Not supported in Redis.
	 *			Only included in order to offer a
	 *			consistent cache API.
	 * @return	array
	 * @see		Redis::info()
	 */
	public function cacheInfo($type = NULL)
	{
		return $this->_redis->info();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get cache metadata
	 *
	 * @param	string	Cache key
	 * @return	array
	 */
	public function getMetadata($key)
	{
		$value = $this->get($key);

		if ($value)
		{
			return array(
				'expire' => time() + $this->_redis->ttl($key),
				'data' => $value
			);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check if Redis driver is supported
	 *
	 * @return	bool
	 */
	public function isSupported()
	{
		if (extension_loaded('redis'))
		{
			return $this->_setup_redis();
		}
		else
		{
			// Logger::debug('The Redis extension must be loaded to use Redis cache.');
			return FALSE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Setup Redis config and connection
	 *
	 * Loads Redis config file if present. Will halt execution
	 * if a Redis connection can't be established.
	 *
	 * @return	bool
	 * @see		Redis::connect()
	 */
	protected function _setup_redis()
	{
		$config = Config::load('redis', NULL, TRUE);

		$config = array_merge(self::$_default_config, $config);

		$this->_redis = new \Redis();

		try
		{
			if ($config['socket_type'] === 'unix')
			{
				$success = $this->_redis->connect($config['socket']);
			}
			else // tcp socket
			{
				$success = $this->_redis->connect($config['host'], $config['port'], $config['timeout']);
			}

			if ( ! $success)
			{
				Logger::error('Cache: Redis connection refused. Check the config.');
				return FALSE;
			}
		}
		catch (RedisException $e)
		{
			Logger::error('Cache: Redis connection refused ('.$e->getMessage().')');
			return FALSE;
		}

		if (isset($config['password']))
		{
			$this->_redis->auth($config['password']);
		}

		if (isset($config['database']))
		{
			$this->_redis->select(intval($config['database']));
		}		

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Class destructor
	 *
	 * Closes the connection to Redis if present.
	 *
	 * @return	void
	 */
	public function __destruct() {
		if ($this->_redis) {
			$this->_redis->close();
		}
	}

}

/* End of file CacheRedis.php */