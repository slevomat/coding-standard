<?php

doSomething(function (string $class) {
	echo $class;

	/** @var Foo $class */
	$foo = $class::foo();

	return $foo;
});
