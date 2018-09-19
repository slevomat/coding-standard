<?php

$foo = new \DateTimeImmutable();

class Boo
{
	public static function getInstance()
	{
		return new self();
	}
}

class Doo
{
	public static function getInstance()
	{
		return new static();
	}
}

$hooClassName = 'Hoo';
$hoo = new $hooClassName();

$classNamesInArray = ['Aoo'];
$aoo = new $classNamesInArray[0]();

$whitespaceBetweenClassNameAndParentheses = new stdClass    ();
$whitespaceBetweenParentheses = new stdClass(    );
$multiLineBetweenParentheses = new stdClass(
);

new $a->{'b'}["c"]->$d['e'][1]();

$x = [
	new stdClass(),
];

$y = [new stdClass()];

$z = new stdClass() ? new stdClass() : new stdClass();

$q = $q ?: new stdClass();
$e = $e ?? new stdClass();

$aa[(string) new stdClass()] = true;

if (new stdClass()) {

}

$aaa = [
	(string) new stdClass() => new stdClass()
];
