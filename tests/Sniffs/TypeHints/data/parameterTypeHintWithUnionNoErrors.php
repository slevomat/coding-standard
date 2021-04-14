<?php // lint >= 8.0

class Whatever
{

	private function withTypeHint(string|int $a)
	{
	}

	/**
	 * @param string|true $a
	 */
	private function withTrue(string|bool $a)
	{
	}

	/**
	 * @param ArrayHash|array|mixed[] $a
	 */
	public function arrayAndArrayHash($a)
	{
	}

}
