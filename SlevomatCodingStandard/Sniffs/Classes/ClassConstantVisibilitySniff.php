<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ClassConstantVisibilitySniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_MISSING_CONSTANT_VISIBILITY = 'MissingConstantVisibility';

	/**
	 * Automatically disables the sniff on unusable version, to be removed when only PHP 7.1+ is supported
	 *
	 * @var bool
	 */
	public $enabled = PHP_VERSION_ID >= 70100;

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		if (!$this->enabled) {
			return [];
		}

		return [
			T_CONST,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $constantPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $constantPointer)
	{
		$tokens = $phpcsFile->getTokens();

		if (count($tokens[$constantPointer]['conditions']) === 0) {
			return;
		}

		$classPointer = array_keys($tokens[$constantPointer]['conditions'])[count($tokens[$constantPointer]['conditions']) - 1];
		if (!in_array($tokens[$classPointer]['code'], [T_CLASS, T_INTERFACE], true)) {
			return;
		}

		$visibilityPointer = TokenHelper::findPreviousEffective($phpcsFile, $constantPointer - 1);
		if (!in_array($tokens[$visibilityPointer]['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)) {
			$phpcsFile->addError(
				sprintf(
					'Constant %s::%s visibility missing.',
					ClassHelper::getFullyQualifiedName($phpcsFile, $classPointer),
					$tokens[TokenHelper::findNextEffective($phpcsFile, $constantPointer + 1)]['content']
				),
				$constantPointer,
				self::CODE_MISSING_CONSTANT_VISIBILITY
			);
		}
	}

}
