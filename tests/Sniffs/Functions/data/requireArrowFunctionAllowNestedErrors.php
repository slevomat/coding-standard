<?php // lint >= 7.4

$a = function ($aa) {
	return function ($bb) use ($aa) {
		return function ($cc) use ($bb, $aa) {
			return $aa + $bb + $cc;
		};
	};
};
