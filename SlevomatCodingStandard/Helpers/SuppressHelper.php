<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class SuppressHelper
{

	const ANNOTATION = '@phpcsSuppress';

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $pointer
	 * @param string $suppressName
	 * @return boolean
	 */
	public static function isSniffSuppressed(\PHP_CodeSniffer_File $codeSnifferFile, $pointer, $suppressName)
	{
		return array_reduce(AnnotationHelper::getAnnotationsByName($codeSnifferFile, $pointer, self::ANNOTATION), function ($carry, $annotationContent) use ($suppressName) {
			if ($annotationContent === $suppressName || strpos($suppressName, sprintf('%s.', $annotationContent)) === 0) {
				$carry = true;
			}
			return $carry;
		}, false);
	}

}
