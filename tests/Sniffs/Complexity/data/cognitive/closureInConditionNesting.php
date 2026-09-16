<?php

function closureInCondition($items)
{
	if (array_filter($items, function ($item) { return $item; }) !== []) { // +1
		foreach ($items as $item) { // +2 (nesting = 1)
			echo $item;
		}
	}
}
