<?php

namespace Foo;

use Dolor;

class Foo
{
	use \SomeClass, \Some\OtherClass;
}

class Ipsum
{
	use /*\Omega, */ Dolor;
}

class Lorem
{
	use \Dolor, \Amet;
}

class LoremConsecteur
{
	use Dolor, \Amet;
}

class Bar
{
	use \Dolor, Amet, Omega;
}

$foo = null;

$boo = function () use ($foo) {

};
