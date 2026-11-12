<?php

if (true) {
	return $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
		'bbbb' :
		'cccccccccccccc';
}

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
	'bbbb' :
	'cccccccccccccc';

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
	'bbbb' :
	func(1, 2);

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
	'bbbb' :
	func([]);

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
	'bbbb' :
	func([1 => 2]);

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
	'bbbb' :
	function () { return; };

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
	'bbbb' :
	(false ? 1 : 2);

$array[$b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
	'bbbb'
	: 'ccccccc'] = 'b';

$a = in_array(
	$b,
	$c,
	true
) ?
	'bbbb' :
	'cccccccccccccc';

$a = in_array(
	$b,
	$c,
	true
)
		? 'bbbb'
		: 'cccccccccccccc';

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb'
? 'bbbb'
  : 'cccccccccccccc';

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb'
		? 'bbbb'
	: 'cccccccccccccc';

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb'
	? 'bbbb' :
	'cccccccccccccc';

function () use ($b) {
	yield $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
		'bbbb' :
		'cccccccccccccc';

	yield from $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ?
		'bbbb' :
		'cccccccccccccc';
};
