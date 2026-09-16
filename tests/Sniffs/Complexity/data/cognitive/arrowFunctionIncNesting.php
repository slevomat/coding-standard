<?php

function arrowFunction($items)
{
	if ($items !== []) { // +1
		return array_map(fn ($item) => $item ? 1 : 2, $items); // +3 (nesting = 2)
	}

	return [];
}
