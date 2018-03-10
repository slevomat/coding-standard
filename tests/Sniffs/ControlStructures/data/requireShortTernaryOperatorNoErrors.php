<?php

$x = $a ?: true;

sprintf('%s', $x ?: 'string');

$z = isset($x) ?: false;

$y = $foo !== null ? $foo : null;

$yy = null !== $foo ? $foo : null;

$foo = $boo
	? something()
	: somethingElse();

$foo = $boo
	? $doo
	: false;

$foo = !$boo
	? true
	: $doo;

$foo = $zz < $id ? $id : 0;

$foo = $array ? $array[0] : '&';

$foo = !$boo ? 0 : $boo[0];
