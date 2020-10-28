<?php $a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccc';

$a = $b ?: true;

$a = $b
	? true
	: false;

$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccccccccc';

$array[$b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccc'] = 'b';

doSomething($b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccc');

// Comment
$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccccccccc';

// phpcs:disable ABC
$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : 'ccccccccccccc';

// comma, double arrow, close array, semicolon in parenthesis
$a = $b === 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb' ? 'bbbb' : func([], [1 => 2], function () { return; });
