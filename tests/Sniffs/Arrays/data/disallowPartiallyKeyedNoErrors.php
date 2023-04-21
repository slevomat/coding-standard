<?php // lint >= 7.4

[];

array();

['foo', 'bar', 'baz'];

array('foo', 'bar', 'baz');

$a = [
	0 => 'zero',
	'foo' => 'foo',
	'bar' => 'bar',
	'baz' => 'baz'
];

array(
	0 => 'zero',
	'foo' => 'foo',
	...$a,
	'bar' => 'bar',
	'baz' => 'baz',
	...$a
);
