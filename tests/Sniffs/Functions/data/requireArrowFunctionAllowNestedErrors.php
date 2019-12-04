<?php // lint >= 7.4

$a = function ($aa) {
	return function ($bb) use ($aa): int {
		return function ($cc) use ($bb, $aa): int {
			return $aa + $bb + $cc;
		};
	};
};
