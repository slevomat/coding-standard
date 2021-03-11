<?php // lint >= 8.0

use Foo\{ Boo, Doo };

foo();

$foo = foo();

function () {
	return false;
};

$x = 'x';

$anonymous = function () {
	return null;
};

fn ($a) => $a;

for (;;) {

}

new class extends Whatever {

};

$fooPropertyValue = $foo->{$fooProperty};

function witMatch($a)
{
	return match ($a) {
		'foo' => 'bar',
	};
}
