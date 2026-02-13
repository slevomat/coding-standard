<?php

use Bar;
use Foo;

try {
	doSomething();
} catch (BException | AException $e) {

}

try {
	doSomething();
} catch (\Ccc | \Aaa | \Bbb $e) {

}

try {
	doSomething();
} catch (\InvalidArgumentException | \Exception $e) {

}

try {
	doSomething();
} catch (Foo\Bbb | Bar\Aaa $e) {

}

try {
	doSomething();
} catch (
	\Ccc |
	\Aaa |
	\Bbb $e
) {

}

try {
	doSomething();
} catch (
	Foo\Bbb |
	Bar\Aaa $e
) {

}

try {
	doSomething();
} catch (
	\InvalidArgumentException |
	\Exception $e
) {

}

try {
	doSomething();
} catch (Ccc | \Aaa | \Bbb\DDD $e) {

}

try {
	doSomething();
} catch (DException | CException | AException $e) {

} catch (\Zzz | \Mmm | \Aaa $e) {

} catch (Foo\Bbb | Bar\Ccc | Bar\Aaa $e) {

}
