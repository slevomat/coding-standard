<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class SuppressHelper
{

	const ANNOTATION = '@phpcsSuppress';

	public static function isSniffSuppressed(\PHP_CodeSniffer_File $codeSnifferFile, int $pointer, string $suppressName): bool
	{
		return array_reduce(AnnotationHelper::getAnnotationsByName($codeSnifferFile, $pointer, self::ANNOTATION), function ($carry, $annotationContent) use ($suppressName) {
			if ($annotationContent === $suppressName || strpos($suppressName, sprintf('%s.', $annotationContent)) === 0) {
				$carry = true;
			}
			return $carry;
		}, false);
	}

}
