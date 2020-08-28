<?php // lint >= 7.4

\array_search(0, [1, 2]);
array_keys([], 0);
array_keys(
	[],
	0,
);

\in_array(0, [], false);
in_array(
	0,
	[],
	false,
);
base64_decode('', false);

in_array(
	0,
	[
		1,
		2,
	],
	false,
);
