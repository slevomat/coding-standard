<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ClassConstantVisibilitySniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_MISSING_CONSTANT_VISIBILITY = 'MissingConstantVisibility';

	/**
	 * @deprecated
	 * @var bool
	 */
	public $enabled = true;

	/** @var bool */
	public $fixable = false;

	/**
	 * @return mixed[]
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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $constantPointer): void
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
		if (in_array($tokens[$visibilityPointer]['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)) {
			return;
		}

		$message = sprintf(
			'Constant %s::%s visibility missing.',
			ClassHelper::getFullyQualifiedName($phpcsFile, $classPointer),
			$tokens[TokenHelper::findNextEffective($phpcsFile, $constantPointer + 1)]['content']
		);

		if ($this->fixable) {
			$fix = $phpcsFile->addFixableError($message, $constantPointer, self::CODE_MISSING_CONSTANT_VISIBILITY);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContentBefore($constantPointer, 'public ');
				$phpcsFile->fixer->endChangeset();
			}
		} else {
			$phpcsFile->addError($message, $constantPointer, self::CODE_MISSING_CONSTANT_VISIBILITY);
		}
	}

}
