<?php

use Bar;
use Foo;

try {
	doSomething();
} catch (AException | BException $e) {

}

try {
	doSomething();
} catch (\Aaa | \Bbb | \Ccc $e) {

}

try {
	doSomething();
} catch (\Exception | \InvalidArgumentException $e) {

}

try {
	doSomething();
} catch (Bar\Aaa | Foo\Bbb $e) {

}

try {
	doSomething();
} catch (
	\Aaa |
	\Bbb |
	\Ccc $e
) {

}

try {
	doSomething();
} catch (
	Bar\Aaa |
	Foo\Bbb $e
) {

}

try {
	doSomething();
} catch (
	\Exception |
	\InvalidArgumentException $e
) {

}

try {
	doSomething();
} catch (\Aaa | \Bbb\DDD | Ccc $e) {

}

try {
	doSomething();
} catch (AException | CException | DException $e) {

} catch (\Aaa | \Mmm | \Zzz $e) {

} catch (Bar\Aaa | Bar\Ccc | Foo\Bbb $e) {

}
