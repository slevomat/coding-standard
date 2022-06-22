<?php // lint >= 8.0

class Whatever
{

	private function withTypeHint(): string|int
	{
	}

	/** @return string|true */
	private function withTrue(): string|bool
	{
	}

	/**
	 * @return ArrayHash|array|mixed[]
	 */
	public function arrayAndArrayHash()
	{
	}

	/**
	 * @return A&B
	 */
	public function intersectionWithoutNativeTypeHint()
	{
	}

	/**
	 * @return A&B
	 */
	public function intersectionWithBroadNativeTypeHint(): A
	{
	}

}
