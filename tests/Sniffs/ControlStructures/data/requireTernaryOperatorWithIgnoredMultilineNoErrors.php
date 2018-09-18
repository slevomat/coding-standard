<?php

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
	return $a
		. $b;
} else {
	return $a;
}

if (true) {
	return $a;
} else {
	return $a
		. $b;
}

if (true) {
	$c = $a
		. $b;
} else {
	$c = $a;
}

if (true) {
	$c = $a;
} else {
	$c = $a
		. $b;
}
