<?php

$string = 'abcdefg'
	. 'abcdefg'
. 'abcdefg'
  . 'abcdefg'
		. 'abcdefg';

$trailing = 'abcdefg' /* comment */ .
	'abcdefg' .
	'abcdefg';

if (true) {
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

$array = [
	'key' => 'abcdefg'
	. 'abcdefg'
			. 'abcdefg',
	'abcdefg' .
		'abcdefg',
];

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
