<?php

try {
	doSomething();
} catch (BException | aException $e) {

}

try {
	doSomething();
} catch (\Aaa | \Bbb\DDD | Ccc $e) {

}

try {
	doSomething();
} catch (\BBB | \Ccc | \aaa $e) {

}