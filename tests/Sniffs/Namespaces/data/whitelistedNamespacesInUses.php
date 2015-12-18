<?php

namespace Baz;

use Dolor;
use Foo\Bar;
use Fooo\Baz;
use Lorem\Ipsum;

$foo = 'foo';

function () use ($foo) {

};

class Amet
{

	use Foo\Bar;
	use Fooo\Baz;
	use Lorem\Ipsum;

}
