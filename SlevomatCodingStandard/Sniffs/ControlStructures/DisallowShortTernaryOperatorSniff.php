<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class DisallowShortTernaryOperatorSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_DISALLOWED_SHORT_TERNARY_OPERATOR = 'DisallowedShortTernaryOperator';

	/** @var bool */
	public $fixable = true;

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_INLINE_THEN,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $inlineThenPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);

		if ($tokens[$nextPointer]['code'] !== T_INLINE_ELSE) {
			return;
		}

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $inlineThenPointer - 1);

		$message = 'Use of short ternary operator is disallowed.';

		if ($tokens[$previousPointer]['code'] !== T_VARIABLE) {
			$phpcsFile->addError($message, $inlineThenPointer, self::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);
			return;
		}

		if (!$this->fixable) {
			$phpcsFile->addError($message, $inlineThenPointer, self::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);
			return;
		}

		$fix = $phpcsFile->addFixableError($message, $inlineThenPointer, self::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addContent($inlineThenPointer, sprintf(' %s ', $tokens[$previousPointer]['content']));

		$phpcsFile->fixer->endChangeset();
	}

}
