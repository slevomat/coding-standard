<?php

doSomething(function (string $class) {
	echo $class;

	/** @var Foo $wrongVariable */
	$foo = $class::foo();

	return $foo;
});
