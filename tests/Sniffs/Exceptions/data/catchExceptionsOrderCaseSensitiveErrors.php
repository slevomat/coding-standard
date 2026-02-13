<?php

try {
	doSomething();
} catch (aException | BException $e) {

}

try {
	doSomething();
} catch (\aaa | \BBB | \Ccc $e) {

}