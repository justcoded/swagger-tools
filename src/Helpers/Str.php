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
	 * @param string $separator  array nested parts separator
	 *
	 * @return string
	 */
	public static function arrkey2path($key, $separator = '.')
	{
		return strtr($key, [
			'/' => '&#47;',
			$separator => '/',
		]);
	}

	/**
	 * Convert directory path into dot-format key.
	 *
	 * Example:
	 *    /user/profile/name/ > user.profile.name
	 *
	 * @param string $path
	 * @param string $separator  array nested parts separator
	 *
	 * @return string
	 */
	public static function path2arrkey($path, $separator = '.')
	{
		return strtr(trim($path, '/'), [
			'/' => $separator,
		]);
	}
}
