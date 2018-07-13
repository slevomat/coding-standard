<?php

use NoError;

class Foo {
	use NoError;
}

$boo = 'boo';

function () use ($boo, $foo) {
	echo (function () use ($foo) {
		return $foo;
	})();
	echo $boo;
};
