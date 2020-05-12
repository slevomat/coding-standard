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
			}
		} catch (Throwable $e) {
		}
	}
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
	if (true) {

	} // If
	doSomething();
};

function () {
	if (true) {

	} // If


	doSomething();
};

function ($foo) {
	foreach ($foo as $bar) {
	    // TODO lorem ipsum
	    /** @var string $baz */
	    foreach ($bar as $baz) {
	        echo $baz;
	    }
	}
};
