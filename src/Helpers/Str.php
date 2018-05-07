<?php

namespace JustCoded\SwaggerTools\Helpers;

/**
 * Class Str
 * String helper functions
 *
 * @package JustCoded\SwaggerTools\Helpers
 */
class Str
{
	/**
	 * Convert dot-format key name into directory path.
	 *
	 * Example:
	 *    user.profile.name > user/profile/name
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public static function dotkey2path($key)
	{
		return strtr($key, [
			'/' => '&#47;',
			'.' => '/',
		]);
	}

	/**
	 * Convert directory path into dot-format key.
	 *
	 * Example:
	 *    /user/profile/name/ > user.profile.name
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function path2dotkey($path)
	{
		return strtr(trim($path, '/'), [
			'/' => '.',
		]);
	}
}
