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

Swagger multi-document merge script

Usage:
    swagger-merge <source-config.yaml> > <destination.yaml>

CLIHELP;
	exit(0);
}

// check file exists.
$filename = $argv[1];
if (! is_file($filename)) {
	echo "Unable to load file {$argv[1]}. Terminating." . PHP_EOL;
	exit(0);
}

$reader = new \JustCoded\SwaggerTools\YamlReader();
$yaml   = $reader->parseMultiFile($filename);
$config = $reader->dump($yaml);

if (! empty($argv[2]) && is_writable(dirname($argv[2]))) {
	file_put_contents($argv[2], $config);
	echo "Specs saved as {$argv[2]}";
} else {
	echo $config;
}
