<?php // lint >= 7.1

try {
	doSomething();
} catch (FirstDoubleException | \Foo\SecondDoubleException $e) {
	throw $e;
} catch (\Foo\Bar\FirstMultipleException | SecondMultipleException | ThirdMultipleException $e) {
	throw $e;
}
