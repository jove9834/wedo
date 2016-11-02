<?php
/**
 * CHttpException class file.
 * 
 * @author    黄文金
 * @copyright Copyright (c) 2014 - 2015, Wedo, Inc. (http://weidu178.com/)
 * @link      http://weidu178.com
 * @since     Version 1.0
 */
namespace Wedo\Exception;

/**
 * CHttpException represents an exception caused by invalid operations of end-users.
 *
 * The HTTP error code can be obtained via {@link statusCode}.
 * Error handlers may use this status code to decide how to format the error page.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system.base
 * @since 1.0
 */
class CHttpException extends CException
{
	/**
	 * @var integer HTTP status code, such as 403, 404, 500, etc.
	 */
	public $statusCode;

	/**
	 * Constructor.
	 * @param integer $status HTTP status code, such as 404, 500, etc.
	 * @param string $message error message
	 * @param integer $code error code
	 */
	public function __construct($status, $message = null, $code = 0)
	{
		$this->statusCode = $status;
		parent::__construct($message, $code);
	}
}
