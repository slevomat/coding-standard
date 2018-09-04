<?php

namespace Test;

use function func_get_args;
use function is_double as is_funny;
use function strlen;

foo();
foo(...$args);
foo(...bar());
foo($a, $b, ...$c);
foo($a, bar($x, ...$y), ...$c);
foo(...(function () { yield 1; })());

func_get_args();

strlen($x);
strlen(foo(...bar()));
strlen(foo(...bar(...baz())));

\is_bool($foo);
is_funny($foo);
is_int(...$foo);

foo\strlen(...$foo);

new Foo();
new Foo(...bar());

$foo->strlen(...$foo);
