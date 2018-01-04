<?php

use Bar\Baz;
use Foo;
use Lorem\Ipsum as LoremIpsum;
use const Lerdorf\IS_BAR;
use const Rasmus\FOO;
use function Lerdorf\isBar;
use function Rasmus\foo;

class FooBar
{

	use BarTrait;

}

$test = 'foo';

function () use ($test) {

};

use Zero;
