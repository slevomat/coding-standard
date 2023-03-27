<?php

$array = [[
	1 => 2,
	3 => 4,
]];
$array = [
	[
		[1 => 2],
		[[1 => 2]],
		[
			[[
				1 => 2,
				2 => 3,
			]],
		],
	],
];
$array = [
	[
		[1 => 2],
		[[1 => 2]],
		[
			static function () {
				return [
					[
						1 => 2,
						2 => 3,
					],
				];
			},
		],
	],
];
$array = array(array(
	1 => 2,
	3 => 4,
));
$array = array(
	array(
		array(1 => 2),
		array(array(1 => 2)),
		array(
			static function () {
				return array(
					array(
						1 => 2,
						2 => 3,
					),
				);
			},
		),
	),
);
