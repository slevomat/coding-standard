<?php

class WithBracedCase
{

	private int $property = 0;

	public function method(int $value): int
	{
		switch ($value) {
			case 1: {
				$result = $this->property;
				break;
			}
			default:
				$result = 0;
		}

		return $result;
	}

}
