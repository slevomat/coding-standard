<?php

function ($values) {
	foreach ($values as $value) {
		try {
			switch ($value) {
				case true:

					doSomething();
					break;

				case false:

					// With comment
					continue 2;


				case null:

					return;
				case 0:

					throw new Exception();

			}
		} catch (Throwable $e) {
		}
	}
};

label:
goto label;


function () {
	$array = [];
	yield from $array;

};

function () {
	if (true) {

		yield [];
		return;


	}
	yield [];

};

function (): Generator {
	$a = yield a();
	$b = yield b();
};

function () {
	$array = [];
	return yield from $array;
};

function () {
	foreach ([] as $value) {
		doSomething($value);

		break; // Break

	}
};

function () {

	// Multiline
	// Comment
	// Multiline
	return true;
};

function () {

	return function () {
		$a = 1;
		$b = 2;

		return $a * $b;
	};
};

function () {
	return new class implements Countable
	{

		public function count()
		{
			$a = 1;
			$b = 2;

			return $a * $b;
		}

	};

};

function () {

	return [
		function () {
			$a = 1;
			$b = 2;

			return $a * $b;
		},
		function () {
			$a = 1;
			$b = 2;

			return $a * $b;
		},
	];

};

function () {
	yield 1;
	return 2;
	yield 3;
};

function () {
	$foo = 1;
	$bar = 2;

	// first comment


	// second comment
	return $foo + $bar;
};
