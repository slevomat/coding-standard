<?php // lint >= 8.4

$foo = new \DateTimeImmutable;

class Boo
{
	public static function getInstance()
	{
		return new self;
	}
}

class Doo
{
	public static function getInstance()
	{
		return new static;
	}
}

$hooClassName = 'Hoo';
$hoo = new $hooClassName;

$classNamesInArray = ['Aoo'];
$aoo = new $classNamesInArray[0];

$classNamesInObject = new stdClass;
$classNamesInObject->xoo = 'Xoo';
$aoo = new $classNamesInObject->xoo;

$coo = new class {

};

$coo = new readonly class {

};

$whitespaceBetweenClassNameAndParentheses = new stdClass   ;

new $a->{'b'}["c"]->$d['e'][1];

$x = [
	new stdClass,
];

$y = [new stdClass];

$z = new stdClass ? new stdClass : new stdClass;

$q = $q ?: new stdClass;
$e = $e ?? new stdClass;

$aa[(string) new stdClass] = true;

if (new stdClass) {

}

$aaa = [
	(string) new stdClass => new stdClass
];

new WithParameters($a);

new WithMultiLineParameters(
	$b
);

new WithComment( /* Comment */ );

new SomeClass()->method();
new SomeClass()?->method();
new SomeClass()::staticMethod();
new SomeClass()[0];
new SomeClass()();
new \Full\Qualified\SomeClass()->method();
new $variable()->method();
new static()->method();
new self()->method();
