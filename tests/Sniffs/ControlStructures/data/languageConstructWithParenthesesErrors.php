<?php

foreach ([true, false] as $value) {
	if (true) {
		continue (1);
	}
	if (false) {
		break (1);
	}
}

echo('a');
print('b');

include('file.php');
include_once('file.php');
require('file.php');
require_once('file.php');

function foo()
{
	return('foo');
}

function boo()
{
	yield([]);
}

try {
	throw(new \Exception());
} catch (\Throwable $e) {

}

die();
exit();

function booo()
{
	yield from ([]);
}
