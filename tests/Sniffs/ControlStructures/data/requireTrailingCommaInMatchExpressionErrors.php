<?php

$foo = match (rand(0, 1)) {
	0 => false,
	1 => true
};

$bar = match (rand(0, 1)) {
	0 => false,
	1 => match($foo) {
		false => 'foobar',
		true => 'foobaz'
	}
};

function foo(): bool
{
	return match (rand(0, 1)) {
		0 => false,
		1 => true
	};
}

function bar(): string
{
	return match (rand(0, 1)) {
		0 => false,
		1 => match (foo()) {
			false => 'foobar',
			true => 'foobaz'
		}
	};
}
