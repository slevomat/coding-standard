<?php

function closureUse($items, $flag)
{
	foreach ($items as $item) { // +1
		$callback = function () use ($flag) { // +0 (but nesting level is now 2)
			if ($flag) { // +3 (nesting = 2)
				return true;
			}

			return false;
		};
	}
}
