<?php

namespace JustCoded\SwaggerTools\Exceptions;

/**
 * InvalidConfigException represents an exception caused by a invalid configuration taken from external resource.
 */
class InvalidConfigException extends \Exception
{
	/**
	 * @return string the user-friendly name of this exception
	 */
	public function getName()
	{
		return 'Invalid Config Value';
	}
}
