<?php // lint >= 7.4

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

	function foo() {
		$test = 'foo';
		function() use ($test) {

		};
	}

}

$test = 'foo';

function () use ($test) {

};

use Zero;

class MyClass
{
    public function __construct() {
        if (
            $this->valid(fn(): bool => 2 > 1)
        ) {

        }
    }

    private function valid(callable $callable): bool
    {
        return $callable();
    }
}
