<?php

if (true) {
	return true;
}

if (true) {
	return true;
} elseif (false) {
	return false;
} else {
	return null;
}

if (true) {
	doSomething();
	return true;
} else {
	return false;
}

if (true) {
	return true;
} else {
	doSomethingElse();
	return false;
}

if (true) {
	doSomething();
} else {
	$a = 'a';
}

if (true) {
	$a = 'a';
} else {
	doSomethingElse();
}

if (true) {
	$a = 'a';
} else {
	$b = 'b';
}

if (true) {
	$a .= 'a';
} else {
	$a = 'a';
}

if (true) {
	$a = 'a';
} else {
	$a .= 'a';
}

if (true) {
	if (false) {
	}
} else {
	return false;
}

if (true) {
	$a = true ? true : false;
} else {
	$a = 'a';
}

if (true) {
	$a = 'a';
} else {
	$a = true ? true : false;
}

if (true) {
	echo 'true';
}

$foo = 1;
$bar = 2;
if (true) {
	$ref = &$foo;
} else {
	$ref = &$bar;
}

if (true) {
	$ref = $foo;
} else {
	$ref = &$bar;
}

if (true) {
	$ref = &$foo;
} else {
	$ref = $bar;
}
