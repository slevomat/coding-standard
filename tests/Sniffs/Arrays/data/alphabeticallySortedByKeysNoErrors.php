<?php // lint >= 7.4

[ /* inline comment */ // opening line comment
	'a' => 'a val',  /* comment */
	'anonymous' => new class {
	    public function log($msg)
	    {
	        return [
	        	'y' => 'y val',
	        	'z' => 'z val',
	        ];
	    }
	},
	'arrow' => fn($x) => array(
		'w' => 'w val',
		'x' => 'x val',
	),
	'b' => 'b val',  // comment
					 // more comment

	// closure comment
	'closure' => static function ($p1, $p2) {
		return ['a', 'b'];
	},
	'nested' => array(
		'a2' => 'a2 val',
		'b2' => 'b2 val',
	)];

[
	1 => 'one',
	'two',
	4 => 'four',
	'five',
];

['d' => 'd val', 'c' => 'c val'];

// test nested array sniff is disabled if ancestor is disabled
// @phpcs:disable SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys.IncorrectKeyOrder
array(
	'b3' => 'b3 val',
	'a3' => 'a3 val',
	// nested array should abide by the parent's @phpcs:disable
	'c2' => array(
		'b4' => 'b4 val',
		'a4' => 'a4 val',
	),
);

[
	'I am',
	'just a list',
	'no keys',
];
