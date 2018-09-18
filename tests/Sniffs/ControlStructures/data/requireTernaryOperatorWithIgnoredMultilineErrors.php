<?php

if (true) {
	return $a . $b;
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
	$c = $a . $b;
} else {
	$c = $a;
}

if (true) {
	$c = $a;
} else {
	$c = $a
		. $b;
}
