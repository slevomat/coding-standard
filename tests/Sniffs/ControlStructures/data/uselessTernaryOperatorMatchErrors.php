<?php // lint >= 8.0

function ($a, $b) {
	$r = $a === 1 ? true : false ?? match ($b) { default => 'fallback' };
};

function ($flag, $x) {
	$r = $flag && match ($x) { default => 1 } !== null ? false : true;
};

function ($x, $a) {
	$r = match ($x) { 1 => $a === 1, default => 'fallback', } ? true : false;
};

function ($x, $a) {
	$r = match ($x) { 0 => 42, $a === 1 ? true : false ?? null => 2, default => 0 };
};

function ($x) {
	$r = match ($x) { 1 => true ? true : false };
};
