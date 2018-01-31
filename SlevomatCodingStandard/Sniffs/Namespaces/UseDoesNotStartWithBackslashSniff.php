<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class UseDoesNotStartWithBackslashSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_STARTS_WITH_BACKSLASH = 'UseStartsWithBackslash';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_USE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $usePointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $usePointer): void
	{
		if (
			UseStatementHelper::isAnonymousFunctionUse($phpcsFile, $usePointer)
			|| UseStatementHelper::isTraitUse($phpcsFile, $usePointer)
		) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		/** @var int $nextTokenPointer */
		$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);

		if ($tokens[$nextTokenPointer]['code'] === T_STRING
			&& ($tokens[$nextTokenPointer]['content'] === 'function' || $tokens[$nextTokenPointer]['content'] === 'const')
		) {
			/** @var int $nextTokenPointer */
			$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $nextTokenPointer + 1);
		}

		if ($tokens[$nextTokenPointer]['code'] !== T_NS_SEPARATOR) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use statement cannot start with a backslash.',
			$nextTokenPointer,
			self::CODE_STARTS_WITH_BACKSLASH
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($nextTokenPointer, '');
		$phpcsFile->fixer->endChangeset();
	}

}
