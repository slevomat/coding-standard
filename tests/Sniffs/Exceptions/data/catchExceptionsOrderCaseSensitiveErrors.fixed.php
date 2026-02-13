<?php

try {
	doSomething();
} catch (BException | aException $e) {

}

try {
	doSomething();
} catch (\BBB | \Ccc | \aaa $e) {

}