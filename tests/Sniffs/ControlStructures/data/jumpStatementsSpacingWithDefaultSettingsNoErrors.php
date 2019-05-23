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
    yield 2 => 3;
    yield 4;

    if (true) {

    }

    yield from [1, 2];
    yield from [3 => 4, 5 => 6];
    yield 7;
    yield from [8, 9];

    return 10;
};

function () {
    return Something::something(
        ['foo' => 'bar'],
        function () {
            $a = 1;
            $b = 2;

            return $a + $b;
        }
    );
};
