<?php

function () {
	return true;
};

function () {
	return;
};

function () {
	yield [];
};

$singularUninflected = new Uninflected(...(static function () use ($uninflected) : iterable {
	yield from $uninflected->getWords();
	yield from Uninflected::getDefaultWords();
})());
