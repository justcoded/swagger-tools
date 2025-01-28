<?php

namespace JustCoded\SwaggerTools;

use JustCoded\SwaggerTools\Exceptions\InvalidFileException;
use JustCoded\SwaggerTools\Helpers\Arr;
use JustCoded\SwaggerTools\Helpers\Str;
use Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Class Yaml
 * Tools to work with yaml.
 *
 * @package JustCoded\SwaggerTools
 */
class YamlReader
{
	/**
	 * Special symbol for building array nested keys
	 *
	 * URLs can contain usual cars like dots or lines, so to a safe key we will use rarely used ASCII symbol
	 */
	const ARR_KEY_SEP = '»';

	/**
	 * Parse multi-document yaml file.
	 *
	 * @param string $filename
	 *
	 * @return array
	 * @throws InvalidFileException
	 */
	public function parseMultiFile($filename)
	{
		if (! is_file($filename)) {
			throw new InvalidFileException('Invalid file: ' . $filename);
		}
		$yaml = $this->parse(file_get_contents($filename));
		$yaml = $this->mergeByReferences($yaml, dirname($filename) . '/');

		return $yaml;
	}

	/**
	 * Check parsed yaml array for $ref's, which links to separate file and merge there content to original array.
	 *
	 * Example:
	 *    $ref: 'definitions.yml#/definitions/MyModel'
	 *        MyModel will be imported into definitions and this ref will be replaced with
	 *    $ref: '#/definitions/MyModel'
	 *
	 * @param array  $yaml
	 * @param string $basedir
	 * @param string $index
	 *
	 * @return mixed
	 * @throws InvalidFileException
	 */
	public function mergeByReferences($yaml, $basedir, $index = '')
	{
		$ks     = static::ARR_KEY_SEP; // Safe nested array key separator.
		$branch = $index ? Arr::get($yaml, $index, null, $ks) : $yaml;
		foreach ($branch as $key => $value) {
			$branch_key = trim("{$index}{$ks}{$key}", $ks);
			if (is_array($value)) {
				$yaml = $this->mergeByReferences($yaml, $basedir, $branch_key);
			} elseif ('$ref' === $key && is_string($value) && 0 !== strpos($value, '#')) {
				[$file, $path] = explode('#', $value, 2);
				if (! is_file($basedir . $file) || ! $ref_yaml_content = file_get_contents($basedir . $file)) {
					throw new InvalidFileException('Reference file missing: ' . $basedir . $file);
				}

				// replace relative path for folder reference (remove ../ part from path).
				$ref_dir_depth = count(explode('/', $file)) - 1;
				if ($ref_dir_depth) {
					$ref_yaml_content = preg_replace(
						'/\\$ref:\s([\'"])?(' . str_repeat('..\\/', $ref_dir_depth) . ')/',
						'$ref: $1',
						$ref_yaml_content
					);
				}

				// local refs inside current file should be replaced with full file path, so they also can be merged.
				$ref_yaml_content = preg_replace(
					'/\\$ref:\s[\\"\\\']#\\/(.*?)[\\\'\\"]/',
					"\$ref: '{$file}#/$1'",
					$ref_yaml_content
				);

				$ref_yaml = $this->parse($ref_yaml_content);
				// get referenced yaml part.
				$ref_branch = Arr::get($ref_yaml, Str::path2arrkey($path, $ks), null, $ks);

				$branch_parent_path = dirname(Str::arrkey2path($index, $ks));
				$ref_parent_path    = dirname(Str::arrkey2path(Str::path2arrkey($path, $ks), $ks));

				if ($branch_parent_path !== $ref_parent_path) {
					Arr::set($yaml, $branch_key, "#$path", $ks);
					$branch_key = Str::path2arrkey($path, $ks);
				} else {
					$branch_key = $index;
				}

				Arr::set($yaml, $branch_key, $ref_branch, $ks);

				// check refs in replaced fragment.
				$yaml = $this->mergeByReferences($yaml, $basedir, $branch_key);
			}
		}

		return $yaml;
	}

	/**
	 * Parse yaml string
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	public function parse($string)
	{
		return YamlParser::parse($string);
	}

	/**
	 * Convert array/input to yaml string
	 *
	 * @param array $input
	 * @param int $inline
	 * @param int $indent
	 * @param int $options
	 *
	 * @return string
	 */
	public function dump(
		$input,
		$inline = 50,
		$indent = 2,
		$options = YamlParser::DUMP_OBJECT_AS_MAP | YamlParser::DUMP_EMPTY_ARRAY_AS_SEQUENCE | YamlParser::DUMP_MULTI_LINE_LITERAL_BLOCK
	) {
		return YamlParser::dump($input, $inline, $indent, $options);
	}
}
