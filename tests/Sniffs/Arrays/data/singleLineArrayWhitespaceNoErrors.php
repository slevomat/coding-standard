<?php

$singleLine = ['a', 'b', 1, 'c', ['d' => '1', 2 => 3, 'e']];

$multiLine = [
	'a',
	'b',
	1,
	'c',
	[
		'd' => '1',
		2 => 3,
		'e',
	],
];

$array = [1 => 2];
$array = [[1 => 2]];
$array = [
	[1 => 2],
];

$array = [1, run([1, 2], 3, [])];
$array = [1,];

[$a, , $c] = $array;

$singleLine = array('a', 'b', 1, 'c', array('d' => '1', 2 => 3, 'e'));

$multiLine = array(
	'a',
	'b',
	1,
	'c',
	array(
		'd' => '1',
		2 => 3,
		'e',
	),
);

$array = array(1 => 2);
$array = array(array(1 => 2));
$array = array(
	array(1 => 2),
);

$array = array(1, run(array(1, 2), 3, array()));
$array = array(1,);
