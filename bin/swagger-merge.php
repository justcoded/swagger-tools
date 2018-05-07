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

$tools = new \JustCoded\SwaggerTools\YamlReader();
$yaml  = $tools->parseMultiFile($filename);
$config = $tools->dump($yaml);

echo $config;