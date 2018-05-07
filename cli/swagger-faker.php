#!/usr/bin/php
<?php

// load composer autoload.
$composerAutoload = [
	__DIR__ . '/../vendor/autoload.php',
	__DIR__ . '/../../../autoload.php',
	__DIR__ . '/../../../vendor/autoload.php',
];
$vendorPath       = null;
foreach ($composerAutoload as $autoload) {
	if (file_exists($autoload)) {
		require $autoload;
		$vendorPath = dirname($autoload);
		break;
	}
}

// check for file argument.
if (empty($argv[1])) {
	echo <<<CLIHELP

Swagger fake models data generator.
Inside definitions or components/schemas search for "x-faker" property and generate "enum" with fake data. This enum can be used by different mock servers to fill data.

Usage:
    swagger-faker <source-config.yaml> [-n=10] [-r] > <destination.yaml>
    
    -n    Number of fake examples to generate for each property
    -r    Fill required array for objects if missing

CLIHELP;
	exit(0);
}

// check file exists.
$filename = $argv[1];
if (! is_file($filename)) {
	echo "Unable to load file {$argv[1]}. Terminating." . PHP_EOL;
	exit(0);
}

// parse opts and set defaults.
$opts = array_merge(
	[
		'n' => 10,
		'r' => false,
	],
	\JustCoded\SwaggerTools\Helpers\Cli::parseArguments($argv)
);

$reader = new \JustCoded\SwaggerTools\YamlReader();
$yaml   = $reader->parse(file_get_contents($filename));

$yaml = \JustCoded\SwaggerTools\Formatter::definitionsFakeEnums($yaml, $opts['n']);
if ($opts['r']) {
	$yaml = \JustCoded\SwaggerTools\Formatter::definitionsRequired($yaml);
}

$config = $reader->dump($yaml);

echo $config;