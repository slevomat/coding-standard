<?php

class Whatever
{
	public function method($items)
	{
		if (true) {           // Comment
			doSomething();
		} elseif (false) {    // Comment
			doSomethingElse();
		} else {              // Comment
			doAnything();
		}                     // Comment

		do { /* Comment */

		} while (true);

		while (true) { /*
			Comment
		*/

		}

		array_map(function () { // phpcs:disable SomeSniff

		}, []); // phpcs:enable

		/** @var string[] $items */
		foreach ($items as $item) { /** @var string $item */

		}

		$array = [
			'true', // True
			'false', // False
		];

		switch (true) { // Switch
			case 1: // Case 1
				break; // Break
			case 2: // Case 2
				continue; // Continue
			default: // Default
				return; // Return
		}

		return true; // True
	}

}
