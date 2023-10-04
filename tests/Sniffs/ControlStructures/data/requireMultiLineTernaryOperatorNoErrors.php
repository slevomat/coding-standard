<?php $a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccc';

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccc';

$a = $b ?: true;

$a = $b
	? true
	: false;

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccccccccc';

$array[$b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccc'] = 'b';

doSomething($b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccc');

$x = doSomething(
	sprintf(
		'String %s %s',
		$y,
		$z !== 1 ? 's' : ''
	),
	$typeHintPointer,
	Whatever::XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
);

$x = doSomething(
	[
		$z !== 1 ? 's' : ''
	],
	$typeHintPointer,
	Whatever::XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
);

$order = [
	UseStatement::TYPE_DEFAULT => 1,
	UseStatement::TYPE_FUNCTION => $this->psr12Compatible ? 2 : 3,
	UseStatement::TYPE_CONSTANT => $this->psr12Compatible ? 3 : 2,
];

// Comment
$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccccccccc';

// phpcs:disable ABC
$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccccccccc';

$a = $b === 'bbbbbbbbbbbbbb' ? 'bbbb' : doSomething([], $c);
