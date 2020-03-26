<?php

class Whatever
{
	public function method($items)
	{
		// Comment
		if (true) {
			doSomething();
		} elseif (false) {
			// Comment
			doSomethingElse();
		} else {
			// Comment
			doAnything();
		}
		// Comment

		/* Comment */
		do {

		} while (true);

		/*
			Comment
		*/
		while (true) {

		}

		array_map(function () {
			// phpcs:disable SomeSniff

		}, []);
		// phpcs:enable

		/** @var string[] $items */
		/** @var string $item */
		foreach ($items as $item) {

		}

		$array = [
			// True
			'true',
			// False
			'false',
		];

		// Switch
		switch (true) {
			// Case 1
			case 1:
				// Break
				break;
			// Case 2
			case 2:
				// Continue
				continue;
			// Default
			default:
				// Return
				return;
		}

		// True
		return true;
	}

}
