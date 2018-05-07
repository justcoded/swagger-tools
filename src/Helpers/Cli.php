<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/7/18
 * Time: 21:26
 */

namespace JustCoded\SwaggerTools\Helpers;


class Cli
{
	/**
	 * Help to parse arguments to key/value
	 *
	 * @param array $arguments Composer arguments.
	 *
	 * @return array
	 */
	public static function parseArguments($arguments = array())
	{
		$args_ready = array();
		$prev_key   = null;
		for ($i = 0; $i < count($arguments); $i++) {
			$arg = $arguments[$i];
			if (empty($arg)) {
				continue;
			}
			if (preg_match('/^--?([a-z]([a-z0-9\-]*))(\=.*)?$/', $arg, $match)) {
				// we have = sign.
				if (! empty($match[3])) {
					$args_ready[$match[1]] = substr($match[3], 1);
					$prev_key              = null;
				} else {
					// we set found key as "true" and remember key index. Maybe we will have value in next passed argument.
					$args_ready[$match[1]] = true;
					$prev_key              = $match[1];
				}
			} else {
				// if prev_key exists this means argument is a value for previous key.
				if ($prev_key) {
					$args_ready[$prev_key] = $arg;
					$prev_key              = null;
				} else {
					$args_ready[] = $arg;
				}
			}
		}

		return $args_ready;
	}
}
