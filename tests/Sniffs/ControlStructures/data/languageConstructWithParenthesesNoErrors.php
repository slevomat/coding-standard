<?php

foreach ([true, false] as $value) {
	if (true) {
		continue 1;
	}
	if (false) {
		break 1;
	}
}

echo 'a';
print 'b';

include 'file.php';
include_once 'file.php';
require 'file.php';
require_once 'file.php';

function foo()
{
	return 'foo';
}

function boo()
{
	yield [];
}

try {
	throw new \Exception();
} catch (\Throwable $e) {

}

function returnCalculationOnStart(): bool
{
	return (10 + 5) === 15;
}

function returnCalculationOnEnd(): bool
{
	return 15 === (10 + 5);
}

function returnConditions(): bool
{
	return (null === true) || (null === false);
}

die(0);
die('Error');
exit(0);
exit('Error');
