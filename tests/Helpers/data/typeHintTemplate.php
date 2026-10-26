<?php

/**
 * @template TClass
 */
class WithClassTemplate
{

	/**
	 * @template TMethod
	 * @param TClass $a
	 * @param TMethod $b
	 */
	public function withMethodTemplate($a, $b)
	{
	}

}

/**
 * @phpstan-template TPhpstan
 * @psalm-template TPsalm
 */
class WithPrefixedTemplates
{

	/**
	 * @return void
	 */
	public function withoutTemplate()
	{
	}

}

/**
 * @template TFunction
 * @param TFunction $a
 */
function withFunctionTemplate($a)
{
}

class WithoutClassDocComment
{

	/**
	 * @template TMethod
	 * @param TMethod $a
	 */
	public function withMethodTemplate($a)
	{
	}

}
