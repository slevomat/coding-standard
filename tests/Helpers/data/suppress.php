<?php

/**
 * @phpcsSuppress Sniff.Sniff.Sniff.check
 */
abstract class IsSuppressed
{

	/**
	 * @phpcsSuppress Sniff.Sniff.Sniff.check
	 */
	const IS_SUPPRESSED = true;

	const IS_NOT_SUPPRESSED = false;

	/**
	 * @phpcsSuppress Sniff.Sniff.Sniff.check
	 */
	private $isSuppressed;

	protected static $isNotSuppressed;

	abstract public function noDocComment($a);

	/**
	 * @see Whatever
	 */
	public static function docCommentWithoutSuppress($b, $c)
	{

	}

	/**
	 * @phpcsSuppress
	 */
	public function invalidSuppress($d)
	{

	}

	/**
	 * @phpcsSuppress Sniff.Sniff.Sniff.check
	 */
	final public function suppressWithFullName()
	{

	}

	/**
	 * @phpcsSuppress Sniff.Sniff.Sniff
	 */
	final public function suppressWithPartialName($e)
	{

	}

	/**
	 * Description description description.
	 *
	 * @param bool $e
	 * @phpcsSuppress Sniff.Sniff.Sniff.check
	 * @return null
	 */
	public static function suppressWithFullDocComment($e)
	{
		return null;
	}

}

trait IsNotSuppressed
{

}
