<?php

namespace JustCoded\SwaggerTools\Exceptions;

/**
 * InvalidFileException represents an exception caused by a missing required file.
 */
class InvalidFileException extends \Exception
{
	/**
	 * @return string the user-friendly name of this exception
	 */
	public function getName()
	{
		return 'Invalid File';
	}
}
