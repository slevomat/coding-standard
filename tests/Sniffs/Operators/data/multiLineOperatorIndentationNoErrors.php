<?php

$singleLine = 'abcdefg' . 'abcdefg' . 'abcdefg';

$string = 'abcdefg'
	. 'abcdefg'
	. 'abcdefg'
	. 'abcdefg';

if (true) {
	$indented = 'abcdefg'
		. 'abcdefg'
		. 'abcdefg';

	return 'abcdefg'
		. 'abcdefg'
		. 'abcdefg';
}

$nestedCall = sprintf(
	'Hello %s',
	'abcdefg'
		. 'abcdefg'
		. 'abcdefg'
);

$callOperands = sprintf('a %s', $x)
	. sprintf(
		'b %s',
		$y
	)
	. 'c';

$array = [
	'key' => 'abcdefg'
		. 'abcdefg'
		. 'abcdefg',
	'abcdefg'
		. 'abcdefg'
		. 'abcdefg',
];

$arrayOperand = $array['key']
	. 'abcdefg' . 'abcdefg';

$commentAfterLeadingOperator = 'abcdefg'
	. // comment
	'abcdefg';

$anon = (string) new class {
	public $prop = 'value';
}
	. 'abcdefg';

switch ($x) {
	case 'foo':
		'first'
			. 'second';
	default:
		'first'
			. 'second';
}

$heredoc = <<<EOD
abcdefg
EOD
. 'abcdefg';

$nowdoc = <<<'EOD'
abcdefg
EOD
. 'abcdefg';

class Foo extends Bar
{

	private const SOME_CONST = 'first-line'
		. 'second-line'
		. 'third-line';

	public function __construct(
		?array $options = null,
		?array $groups = null,
		?array $payload = null,
		string $noPermissionMessage = 'You cannot change the role of a user for a higher access level '
			. 'than you have.'
	) {
		parent::__construct($options, $groups, $payload, $noPermissionMessage);
	}

}

$fn = fn () =>
	'a'
		. 'b';

function () {
	yield 'a'
		. 'b';

	yield from 'a'
		. 'b';
};
