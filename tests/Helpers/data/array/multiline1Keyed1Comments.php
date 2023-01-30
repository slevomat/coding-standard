<?php // lint >= 7.4

// Multi-line / NOT keyed

[ /* inline comment */ // opening line comment
	'b' => 'b val',  // comment
					 // more 'b' comment
	'a' . strtolower('param') => 'a val',  /* comment */
	// closure comment
	'closure' => static function ($p1, $p2) {
		return ['a2', 'b2'];
	},
	'nested' => array(
		'b3' => 'b3 val',
		'a3' => 'a3 val',
	),
	'arrow' => fn($x) => strtolower($x),
	'anonymous' => new class {
		public function log($msg)
		{
			return true;
		}
	},
	'foo', 'bar'];
