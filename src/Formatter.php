<?php

namespace JustCoded\SwaggerTools;

/**
 * Class Formatter
 * Prepare extra swagger configuration properties to use in different mock tools
 *
 * @package JustCoded\SwaggerTools
 */
class Formatter
{
	public static function draft($yaml)
	{
		// apply enum values for fake responses.
		if (isset($yaml['definitions']) || isset($yaml['components']['schemas'])) {
			$def_key = isset($yaml['definitions']) ? 'definitions' : 'components/schemas';
			$definitions = array_get($yaml, $def_key);
			foreach ($definitions as $model => $schema) {
				if ('object' === array_get($schema, 'type') && ! empty($schema['properties'])) {
					// generate fake values.
					foreach ($schema['properties'] as $property => $params) {
						if (empty($params['x-faker'])) {
							continue;
						}
						$formatter = $params['x-faker'];
						$formatter_config = $params['x-faker-config'] ?? [];
						$enum = array_fill(0, 10, null);
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
						} catch (Exception $e) {
							die("Wrong faker formatter '{$formatter}' in {$model}->{$property}");
						}
						array_set($yaml, "$def_key.$model.properties.$property.enum", $enum);
					}
					// fill required array.
					if (! isset($schema['required'])) {
						array_set($yaml, "$def_key.$model.required", array_keys($schema['properties']));
					}
				}
			}
		}
	}
}