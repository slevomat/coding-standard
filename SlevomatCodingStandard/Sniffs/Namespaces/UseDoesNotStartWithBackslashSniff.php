<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function in_array;
use function ltrim;
use const T_USE;

class UseDoesNotStartWithBackslashSniff implements Sniff
{

	public const CODE_STARTS_WITH_BACKSLASH = 'UseStartsWithBackslash';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_USE,
		];
	}

	public function process(File $phpcsFile, int $usePointer): void
	{
		if (!UseStatementHelper::isImportUse($phpcsFile, $usePointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		/** @var int $nextTokenPointer */
		$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);

		if (
			in_array($tokens[$nextTokenPointer]['code'], TokenHelper::NAME_TOKEN_CODES, true)
			&& (
				$tokens[$nextTokenPointer]['content'] === 'function'
				|| $tokens[$nextTokenPointer]['content'] === 'const'
			)
		) {
			/** @var int $nextTokenPointer */
			$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $nextTokenPointer + 1);
		}

		if (!NamespaceHelper::isFullyQualifiedPointer($phpcsFile, $nextTokenPointer)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use statement cannot start with a backslash.',
			$nextTokenPointer,
			self::CODE_STARTS_WITH_BACKSLASH,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::replace(
			$phpcsFile,
			$nextTokenPointer,
			ltrim($tokens[$nextTokenPointer]['content'], '\\'),
		);
		$phpcsFile->fixer->endChangeset();
	}

}
