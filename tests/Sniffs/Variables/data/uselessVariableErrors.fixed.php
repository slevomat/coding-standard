<?php

function () {
	return true;
};

function () {
	return $b + 1;
};

function () {
	return $c - 1;
};

function () {
	return $d * 1;
};

function () {
	return $e / 1;
};

function () {
	return $f ** 1;
};

function () {
	return $g % 1;
};

function () {
	return $h & 1;
};

function () {
	return $i | 1;
};

function () {
	return $j ^ 1;
};

function () {
	return $k << 1;
};

function () {
	return $l >> 1;
};

function () {
	return $m . 1;
};

function sameVariableInDifferentScope() {
	return array_map(function () {
		return $n + 1;
	}, []);
}

function differentVariableAfterReturn() {
	$o = 0;

	if (true) {
		return $o;
	}

	$p = 1;
}

function moreVariables() {
	try {
		$result = true;
	} catch (Throwable $e) {
		$result = false;
	}

	return $result;
}

function moreVariableOneWithoutAssigment() {
	$result++;
	return 10;
}
