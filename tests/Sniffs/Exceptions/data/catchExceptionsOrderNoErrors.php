<?php

try {
	doSomething();
} catch (AException | BException $e) {

}

try {
	doSomething();
} catch (\AException | \BException $e) {

}

try {
	doSomething();
} catch (AException $e) {

}

try {
	doSomething();
} catch (\Aaa | \Bbb | \Ccc $e) {

}

try {
	doSomething();
} catch (Bar\Aaa | Foo\Bbb $e) {

}

try {
	doSomething();
} catch (AException | BException | CException $e) {

} catch (\Ddd | \Eee | \Fff $e) {

} catch (\Ggg | \Hhh $e) {

}
