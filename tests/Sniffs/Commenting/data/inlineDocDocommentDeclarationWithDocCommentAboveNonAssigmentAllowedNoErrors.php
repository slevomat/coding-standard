<?php

doSomething(function (string $class) {
	echo $class;

	/** @var Foo $class */
	$foo = $class::foo();

	return $foo;
});

function ($record) {
	foreach ($record['context']['attachments'] as $attachment) {
		/** @var Whatever $attachment */

	}
};

