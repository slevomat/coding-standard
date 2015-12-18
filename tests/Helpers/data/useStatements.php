<?php

use Bar\Baz;
use Foo;
use Lorem\Ipsum as LoremIpsum;

class FooBar
{

	use BarTrait;

}

$test = 'foo';

function () use ($test) {

};

use Zero;
