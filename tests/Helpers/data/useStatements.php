<?php

use Bar\Baz;
use Foo;
use Lorem\Ipsum as LoremIpsum;
use const Rasmus\FOO_CONSTANT;
use function Lerdorf\isBar;

class FooBar
{

	use BarTrait;

}

$test = 'foo';

function () use ($test) {

};

use Zero;
