<?php

namespace Test;

use function array_slice;
use function strlen;
use function in_array;
use function is_double as is_funny;

strlen(...$foo);
strlen(...foo());
strlen(...foo(...bar()));

in_array(...$foo);
in_array($foo, ...$bar);
in_array($foo, $bar, ...$baz);
in_array(...foo($bar, ...baz()));

in_array(
	$foo,
	(function () {
		return array_slice(
			foo(),
			...(function () {
				yield strlen(...baz());
			})()
		);
	})(),
	true
);

\is_bool(...foo());
\strlen(...foo());
is_funny(...foo());
