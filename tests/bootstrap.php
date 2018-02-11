<?php

if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}

define('LOCKS_DIR', __DIR__ . '/locks');
@mkdir(LOCKS_DIR);

Tester\Environment::setup();

