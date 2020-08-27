<?php

class Whatever
{

	public function parameterAndReturn(int $a): bool
	{
		return true;
	}

	public function onlyParameter(int $a): void
	{
	}

	public function onlyNullableParameter(?int $a): void
	{
	}

	public function onlyReturn(): void
	{
	}

	public function moreParametersOneWithoutAnnotation(int $a, bool $b): void
	{

	}

}
