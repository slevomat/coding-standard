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

[
	'bail',
	'array',
	'required',
	static function (array $value): array {
		foreach ($value as $x => $z) {
			$x + $z;
		}
	},
];

['newsletter' => [], 'campaign' => [], 'other' => []];

$data = [
	'contactId' => 'string',
	'updates' => [
		[
			'update' => [['__tpe' => 'Personal'], 'email@domain.tld'],
			'__tpe' => 'ContactEmailUpdate',
		],
	]];
