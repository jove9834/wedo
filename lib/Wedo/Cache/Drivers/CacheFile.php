<?php
/**
 * File Cache
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Cache\Drivers;

use Wedo\Config;
use Wedo\Cache\CacheInterface;
use Wedo\FileSystem;

/**
 * File Cache
  */
class CacheFile implements CacheInterface {
	/**
	 * 文件操作对象
	 *
	 * @var Wedo\FileSystem
	 */
	protected $_filesystem;
	/**
	 * Directory in which to save cache files
	 *
	 * @var string
	 */
	protected $_cache_path;

	/**
	 * Initialize file-based cache
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$path = Config::get('cache_path');
		$this->_cache_path = empty($path) ? DATA_PATH . '/cache/' : $path;
		wd_mkdirs($this->_cache_path);
		$this->_filesystem = new Filesystem();
	}

	/**
	 * Fetch from cache
	 *
	 * @param	string	$id	Cache ID
	 * @return	mixed	Data on success, FALSE on failure
	 */
	public function get($id)
	{
		$data = $this->_get($id);
		return is_array($data) ? $data['data'] : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Save into cache
	 *
	 * @param	string	$id	Cache ID
	 * @param	mixed	$data	Data to store
	 * @param	int	$ttl	Time to live in seconds
	 * @param	bool	$raw	Whether to store the raw value (unused)
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function set($id, $data, $ttl = 0, $raw = FALSE)
	{
		$contents = array(
			'time'		=> time(),
			'ttl'		=> $ttl,
			'data'		=> $data
		);

		$id = $this->id2file($id);

		if ($this->_filesystem->put($this->_cache_path . $id, serialize($contents)))
		{
			@chmod($this->_cache_path . $id, 0660);
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param	mixed	unique identifier of item in cache
	 * @return	bool	true on success/false on failure
	 */
	public function delete($id) {
		$id = $this->id2file($id);
		$file = $this->_cache_path.$id;
		$files = glob($file);
		if (! $files) {
			return TRUE;
		}

		foreach ($files as $f) {
			$this->_filesystem->delete($f);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to add
	 * @return	New value on success, FALSE on failure
	 */
	public function increment($id, $offset = 1)
	{
		$data = $this->_get($id);

		if ($data === FALSE OR ! is_int($data['data']))
		{
			return FALSE;
		}

		$new_value = $data['data'] + $offset;
		return $this->set($id, $new_value, $data['ttl'])
			? $new_value
			: FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to reduce by
	 * @return	New value on success, FALSE on failure
	 */
	public function decrement($id, $offset = 1)
	{
		$data = $this->_get($id);

		if ($data === FALSE OR ! is_int($data['data']))
		{
			return FALSE;
		}

		$new_value = $data['data'] - $offset;
		return $this->set($id, $new_value, $data['ttl'])
			? $new_value
			: FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the Cache
	 *
	 * @return	bool	false on failure/true on success
	 */
	public function clean()
	{
		$files = $this->_filesystem->files($this->_cache_path);
		return $this->_filesystem->delete($files);
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * Not supported by file-based caching
	 *
	 * @param	string	user/filehits
	 * @return	mixed	FALSE
	 */
	public function cacheInfo($type = NULL)
	{
		return $this->_filesystem->allFiles($this->_cache_path);
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
		if ( ! file_exists($this->_cache_path . $id))
		{
			return FALSE;
		}

		$data = unserialize(file_get_contents($this->_cache_path.$id));

		if (is_array($data))
		{
			$mtime = filemtime($this->_cache_path.$id);

			if ( ! isset($data['ttl']))
			{
				return FALSE;
			}

			return array(
				'expire' => $mtime + $data['ttl'],
				'mtime'	 => $mtime
			);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Is supported
	 *
	 * In the file driver, check to see that the cache directory is indeed writable
	 *
	 * @return	bool
	 */
	public function isSupported()
	{
		return $this->_filesystem->isWritable($this->_cache_path);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get all data
	 *
	 * Internal method to get all the relevant data about a cache item
	 *
	 * @param	string	$id	Cache ID
	 * @return	mixed	Data array on success or FALSE on failure
	 */
	protected function _get($id)
	{
		$id = $this->id2file($id);
		if ( ! file_exists($this->_cache_path . $id)) {
			return FALSE;
		}

		$data = unserialize(file_get_contents($this->_cache_path . $id));
		if ($data['ttl'] > 0 && time() > $data['time'] + $data['ttl']) {
			unlink($this->_cache_path . $id);
			return FALSE;
		}

		return $data;
	}

	protected function id2file($id) {
		// return md5($id);
		return $id;
	}
}