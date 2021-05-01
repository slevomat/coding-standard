<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;
use function count;
use function sprintf;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_INTERFACE;
use const T_TRAIT;

class MethodPerClassLimitSniff implements Sniff
{

	public const CODE_METHOD_PER_CLASS_LIMIT = 'MethodPerClassLimit';

	/** @var int */
	public $maxMethodCount = 10;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_CLASS, T_ANON_CLASS, T_TRAIT, T_INTERFACE];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $classPointer
	 */
	public function process(File $phpcsFile, $classPointer): void
	{
		$numberOfMethods = count(ClassHelper::getMethodPointers($phpcsFile, $classPointer));
		if ($numberOfMethods <= $this->maxMethodCount) {
			return;
		}
		$errorMessage = sprintf(
			'%s has too many methods: %d. Can be up to %d methods.',
			$phpcsFile->getTokens()[$classPointer]['content'],
			$numberOfMethods,
			$this->maxMethodCount
		);
		$phpcsFile->addError($errorMessage, $classPointer, self::CODE_METHOD_PER_CLASS_LIMIT);
	}

}
