<?php

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

call_user_func(function ($a, $b) {
	doSomething($a, $b);
});
