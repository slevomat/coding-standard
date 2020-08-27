<?php

class Whatever
{

	/**
	 * @param int $a
	 * @return bool
	 */
	public function parameterAndReturn(int $a): bool
	{
		return true;
	}

	/**
	 * @param int $a
	 */
	public function onlyParameter(int $a): void
	{
	}

	/**
	 * @param null|int $a
	 */
	public function onlyNullableParameter(?int $a): void
	{
	}

	/**
	 * @return void
	 */
	public function onlyReturn(): void
	{
	}

	/**
	 * @param bool $b
	 */
	public function moreParametersOneWithoutAnnotation(int $a, bool $b): void
	{

	}

}
