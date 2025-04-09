<?php

if ($f === null) {
	$f = 1;
}

function () use ($g) {
	if ($g === null) {
		$g = true;
	}
};
