<?php

use Some\ObjectPrototype;
use function strlen;
use const DATE_ATOM;

class Foo {

	use \BarTrait;

}

trait Bar {

	use \FooTrait;

}
