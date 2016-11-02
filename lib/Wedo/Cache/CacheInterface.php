<?php
/**
 * Cache 接口
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Cache;

/**
 * Cache 接口
 */
interface CacheInterface {
	/**
	 * 根据Key获取缓存数据
	 *
	 * @param string $key key
	 * @return mixed
	 */
	public function get($key);
	
	/**
	 * 缓存数据
	 *
	 * @param string  $key  key
	 * @param mixed   $data 缓存数据
	 * @param integer $ttl  有效期, 0为永久有效
	 * @param boolean $raw  有效期, 0为永久有效
	 * @return mixed
	 */
	public function set($key, $data, $ttl = 0, $raw = FALSE);

	/**
	 * Delete from Cache
	 *
	 * @param string $key Cache Key
	 * @return boolean TRUE on success, FALSE on failure
	 */
	public function delete($key);
	
	/**
	 * Increment a raw value
	 *
	 * @param	string	$key	Cache ID
	 * @param	int	$offset	Step/value to add
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function increment($key, $offset = 1);

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 *
	 * @param	string	$key	Cache ID
	 * @param	int	$offset	Step/value to reduce by
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function decrement($key, $offset = 1);

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function clean();
	
	public function cacheInfo($type = NULL);
	
	
	public function getMetadata($id);

	
	/**
	 * 是否支持
	 *
	 * @return	boolean
	 */
	public function isSupported();

}

/* End of file Cache.php */