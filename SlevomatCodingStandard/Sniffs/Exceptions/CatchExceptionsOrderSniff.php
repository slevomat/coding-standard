<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function usort;
use const T_BITWISE_OR;
use const T_CATCH;

class CatchExceptionsOrderSniff implements Sniff
{

	public const CODE_INCORRECT_ORDER = 'IncorrectOrder';

	public bool $caseSensitive = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CATCH,
		];
	}

	public function process(File $phpcsFile, int $catchPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$catchToken = $tokens[$catchPointer];

		/** @var int $parenthesisOpenerPointer */
		$parenthesisOpenerPointer = $catchToken['parenthesis_opener'];
		/** @var int $parenthesisCloserPointer */
		$parenthesisCloserPointer = $catchToken['parenthesis_closer'];

		/** @var list<array{name: string, startPointer: int, endPointer: int}> $exceptions */
		$exceptions = [];
		$nameEndPointer = $parenthesisOpenerPointer;

		do {
			$nameStartPointer = TokenHelper::findNext(
				$phpcsFile,
				[T_BITWISE_OR, ...TokenHelper::NAME_TOKEN_CODES],
				$nameEndPointer + 1,
				$parenthesisCloserPointer,
			);

			if ($nameStartPointer === null) {
				break;
			}

			if ($tokens[$nameStartPointer]['code'] === T_BITWISE_OR) {
				/** @var int $nameStartPointer */
				$nameStartPointer = TokenHelper::findNextEffective($phpcsFile, $nameStartPointer + 1, $parenthesisCloserPointer);
			}

			$pointerAfterNameEndPointer = TokenHelper::findNextExcluding($phpcsFile, TokenHelper::NAME_TOKEN_CODES, $nameStartPointer + 1);
			$nameEndPointer = $pointerAfterNameEndPointer === null ? $nameStartPointer : $pointerAfterNameEndPointer - 1;

			$exceptions[] = [
				'name' => TokenHelper::getContent($phpcsFile, $nameStartPointer, $nameEndPointer),
				'startPointer' => $nameStartPointer,
				'endPointer' => $nameEndPointer,
			];
		} while (true);

		if (count($exceptions) <= 1) {
			return;
		}

		$sortedNames = [];
		foreach ($exceptions as $exception) {
			$sortedNames[] = $exception['name'];
		}
		usort($sortedNames, fn (string $a, string $b): int => NamespaceHelper::compareStatements($a, $b, $this->caseSensitive));

		$originalNames = [];
		foreach ($exceptions as $exception) {
			$originalNames[] = $exception['name'];
		}

		if ($sortedNames === $originalNames) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Caught exception types are not in alphabetical order.',
			$catchPointer,
			self::CODE_INCORRECT_ORDER,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		foreach ($exceptions as $i => $exception) {
			FixerHelper::change($phpcsFile, $exception['startPointer'], $exception['endPointer'], $sortedNames[$i]);
		}

		$phpcsFile->fixer->endChangeset();
	}

}
