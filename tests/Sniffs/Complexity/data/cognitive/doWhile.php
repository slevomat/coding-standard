<?php

function doWhile($var)
{
	try {
		if (true) { // +1
			do {  // +2 (nesting=1)
				for ($i = 0; $i < 10; $i++) {  // +3 (nesting=2)
				}
			} while (true); // don't increment!
		}
	} catch (\Exception | \Exception $exception) { // +1
		if (true) { } // +2 (nesting=1)
	}
}
