<?php

if (true) {

} elseif (false) {

}

if (true) {

}

if (true) {

} else {

}

if (false) {

} else {

};

while (true) {

}

while (!true) {
};

do {

} while (true);

function () {
	for ($i = 0; $i < 10; $i++) {
		if (true) {

		} // Fucking line comment

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
	// phpcs:disable
	$contents = [];
	// phpcs:enable

	foreach ($contents as $key => $value) {
	}
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

function () {
	switch (true) {
		default:
			switch (true) {
				default:
					break;
			}
	}
};

switch ($foo) {
    case 'bar':

    case 'baz':
        return true;

    case 'lorem':
        return false;
}

if (true) {

}
