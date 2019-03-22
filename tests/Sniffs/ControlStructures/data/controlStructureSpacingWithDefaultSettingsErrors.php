<?php


if (true) {

} elseif (false) {

}
if (true) {

}



if (true) {

} else {

}
while (true) {

}
do {

} while (true);

function () {

	for ($i = 0; $i < 10; $i++) {


		if (true) {

		}
		// With line comment
		if (false) {

		}


	}

};

function ($values) {

	foreach ($values as $value) {


		/**
		 * With doccomment
		 */
		if (true) {

		}
		/*
		 * With block comment
		 */
		if (false) {

		}
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
			// Don't care
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
	doSomething(); // Do something
	if (true) {

	}
};

function () {
	doSomething(); // Do something


	if (true) {

	}
};

function () {
	foreach ([] as $value) {
		doSomething($value);

		break; // Break

	}
};

function () {
	if (true) {

	} // If
	doSomething();
};

function () {
	if (true) {

	} // If


	doSomething();
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
