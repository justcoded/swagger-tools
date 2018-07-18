<?php

namespace JustCoded\SwaggerTools\Helpers;

/**
 * Class Arr
 * Array helper functions.
 *
 * @package JustCoded\SwaggerTools\Helpers
 */
class Arr
{
	/**
	 * Retrieves the value of an array element or object property with the given key or property name.
	 * If the key does not exist in the array or object, the default value will be returned instead.
	 * The key may be specified in a dot format to retrieve the value of a sub-array or the property
	 * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
	 * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
	 * or `$array->x` is neither an array nor an object, the default value will be returned.
	 * Note that if the array already has an element `x.y.z`, then its value will be returned
	 * instead of going through the sub-arrays.
	 * Below are some usage examples,
	 * ~~~
	 * // working with array
	 * $username = Arr::get($_POST, 'username');
	 * // working with object
	 * $username = Arr::get($user, 'username');
	 * // working with anonymous function
	 * $fullName = Arr::get($user, function ($user, $defaultValue) {
	 *     return $user->firstName . ' ' . $user->lastName;
	 * });
	 * // using dot format to retrieve the property of embedded object
	 * $street = Arr::get($users, 'address.street');
	 * ~~~
	 *
	 * @param array|object    $array array or object to extract value from
	 * @param string|\Closure $key key name of the array element, or property name of the object,
	 * or an anonymous function returning the value. The anonymous function signature should be:
	 * `function($array, $defaultValue)`.
	 * @param null|mixed      $default the default value to be returned if the specified array key does not exist. Not used when
	 * getting value from an object.
	 * @param string          $key_separator nested key parts separator
	 *
	 * @return mixed the value of the element if found, default value otherwise
	 */
	public static function get($array, $key, $default = null, $key_separator = '.')
	{
		if ($key instanceof \Closure) {
			return $key($array, $default);
		}

		if (is_array($array) && array_key_exists($key, $array)) {
			return $array[$key];
		}

		if (($pos = strrpos($key, $key_separator)) !== false) {
			$array = Arr::get($array, substr($key, 0, $pos), $default, $key_separator);
			$key   = substr($key, $pos + strlen($key_separator));
		}

		if (is_object($array)) {
			return isset($array->$key) ? $array->$key : $default;
		} elseif (is_array($array)) {
			return array_key_exists($key, $array) ? $array[$key] : $default;
		} else {
			return $default;
		}
	}


	/**
	 * Writes a value into an associative array at the key path specified.
	 * If there is no such key path yet, it will be created recursively.
	 * If the key exists, it will be overwritten.
	 *
	 * ```php
	 *  $array = [
	 *      'key' => [
	 *          'in' => [
	 *              'val1',
	 *              'key' => 'val'
	 *          ]
	 *      ]
	 *  ];
	 * ```
	 *
	 * The result of `array_set($array, 'key.in.0', ['arr' => 'val']);` will be the following:
	 *
	 * ```php
	 *  [
	 *      'key' => [
	 *          'in' => [
	 *              ['arr' => 'val'],
	 *              'key' => 'val'
	 *          ]
	 *      ]
	 *  ]
	 *
	 * ```
	 *
	 * The result of
	 * `Arr::set($array, 'key.in', ['arr' => 'val']);` or
	 * `Arr::set($array, ['key', 'in'], ['arr' => 'val']);`
	 * will be the following:
	 *
	 * ```php
	 *  [
	 *      'key' => [
	 *          'in' => [
	 *              'arr' => 'val'
	 *          ]
	 *      ]
	 *  ]
	 * ```
	 *
	 * @param array             $array the array to write the value to
	 * @param string|array|null $path the path of where do you want to write a value to `$array`
	 * the path can be described by a string when each key should be separated by a dot
	 * you can also describe the path as an array of keys
	 * if the path is null then `$array` will be assigned the `$value`
	 * @param mixed             $value the value to be written
	 * @param string            $key_separator nested key parts separator
	 */
	public static function set(&$array, $path, $value, $key_separator = '.')
	{
		if ($path === null) {
			$array = $value;

			return;
		}

		$keys = is_array($path) ? $path : explode($key_separator, $path);

		while (count($keys) > 1) {
			$key = array_shift($keys);
			if (! isset($array[$key])) {
				$array[$key] = [];
			}
			if (! is_array($array[$key])) {
				$array[$key] = [$array[$key]];
			}
			$array = &$array[$key];
		}

		$array[array_shift($keys)] = $value;
	}
}