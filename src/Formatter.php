<?php

namespace JustCoded\SwaggerTools;

use JustCoded\SwaggerTools\Exceptions\InvalidConfigException;
use JustCoded\SwaggerTools\Helpers\Arr;

/**
 * Class Formatter
 * Prepare extra swagger configuration properties to use in different mock tools
 *
 * @package JustCoded\SwaggerTools
 */
class Formatter
{
	public static function definitionsFakeEnums($yaml, $num, $cleanup = false)
	{
		$faker = \Faker\Factory::create();
		
		// apply enum values for fake responses.
		if (isset($yaml['definitions']) || isset($yaml['components']['schemas'])) {
			$def_key = isset($yaml['definitions']) ? 'definitions' : 'components.schemas';
			$definitions = Arr::get($yaml, $def_key);
			foreach ($definitions as $model => $schema) {
				if ('object' === Arr::get($schema, 'type') && ! empty($schema['properties'])) {
					// generate fake values.
					foreach ($schema['properties'] as $property => $params) {
						if (empty($params['x-faker'])) {
							continue;
						}
						$formatter = $params['x-faker'];
						$formatter_config = $params['x-faker-config'] ?? [];
						$enum = array_fill(0, $num, null);
						try {
							$enum = array_map(
								function () use ($faker, $formatter, $formatter_config) {
									if (empty($formatter)) {
										return $faker->$formatter;
									} else {
										return call_user_func_array([$faker, $formatter], $formatter_config);
									}
								},
								$enum
							);
						} catch (\Exception $e) {
							throw new InvalidConfigException("Wrong faker formatter '{$formatter}' in {$model}->{$property}");
						}

						$definitions[$model]['properties'][$property]['enum'] = $enum;

						if ($cleanup) {
							unset($definitions[$model]['properties'][$property]['x-faker']);
							if (isset($params['x-faker-config'])) {
								unset($definitions[$model]['properties'][$property]['x-faker-config']);
							}
						}
					}
				}
			}
			Arr::set($yaml, $def_key, $definitions);
		}
		return $yaml;
	}
	
	public static function definitionsRequired($yaml)
	{
		// apply enum values for fake responses.
		if (isset($yaml['definitions']) || isset($yaml['components']['schemas'])) {
			$def_key = isset($yaml['definitions']) ? 'definitions' : 'components.schemas';
			$definitions = Arr::get($yaml, $def_key);
			foreach ($definitions as $model => $schema) {
				if ('object' === Arr::get($schema, 'type') && ! empty($schema['properties'])) {
					// fill required array.
					if (! isset($schema['required'])) {
						Arr::set($yaml, "$def_key.$model.required", array_keys($schema['properties']));
					}
				}
			}
		}
		return $yaml;
	}
}
