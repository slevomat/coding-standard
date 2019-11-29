<?php // lint >= 7.4

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
