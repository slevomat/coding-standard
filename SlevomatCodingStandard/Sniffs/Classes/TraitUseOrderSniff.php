<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function usort;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_ENUM;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_TRAIT;

class TraitUseOrderSniff implements Sniff
{

	public const CODE_INCORRECT_ORDER = 'IncorrectOrder';

	public bool $caseSensitive = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CLASS,
			T_ANON_CLASS,
			T_TRAIT,
			T_ENUM,
		];
	}

	public function process(File $phpcsFile, int $classPointer): void
	{
		$usePointers = ClassHelper::getTraitUsePointers($phpcsFile, $classPointer);

		if (count($usePointers) === 0) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		foreach ($usePointers as $usePointer) {
			$this->processCommaSeparatedUse($phpcsFile, $classPointer, $usePointer);
		}

		if (count($usePointers) <= 1) {
			return;
		}

		$uses = [];

		foreach ($usePointers as $usePointer) {
			/** @var int $nameStartPointer */
			$nameStartPointer = TokenHelper::findNext($phpcsFile, TokenHelper::NAME_TOKEN_CODES, $usePointer + 1);

			$name = $tokens[$nameStartPointer]['content'];

			/** @var int $endPointer */
			$endPointer = TokenHelper::findNextLocal($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $usePointer + 1);

			if ($tokens[$endPointer]['code'] === T_OPEN_CURLY_BRACKET) {
				$endPointer = $tokens[$endPointer]['bracket_closer'];
			}

			$uses[] = [
				'name' => $name,
				'contentStartPointer' => $nameStartPointer,
				'contentEndPointer' => $endPointer,
			];
		}

		$sortedUses = $uses;

		usort($sortedUses, fn (array $a, array $b): int => $this->compare($a['name'], $b['name']));

		$isSorted = true;

		for ($i = 0; $i < count($uses); $i++) {
			if ($uses[$i]['name'] !== $sortedUses[$i]['name']) {
				$isSorted = false;
				break;
			}
		}

		if ($isSorted) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Incorrect order of trait use statements.', $classPointer, self::CODE_INCORRECT_ORDER);

		if (!$fix) {
			return;
		}

		$sortedContents = [];

		foreach ($sortedUses as $use) {
			$sortedContents[] = TokenHelper::getContent($phpcsFile, $use['contentStartPointer'], $use['contentEndPointer']);
		}

		$phpcsFile->fixer->beginChangeset();

		foreach ($uses as $i => $use) {
			FixerHelper::change($phpcsFile, $use['contentStartPointer'], $use['contentEndPointer'], $sortedContents[$i]);
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function compare(string $a, string $b): int
	{
		return NamespaceHelper::compareStatements($a, $b, $this->caseSensitive);
	}

	private function processCommaSeparatedUse(File $phpcsFile, int $classPointer, int $usePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $endPointer */
		$endPointer = TokenHelper::findNextLocal($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $usePointer + 1);

		$namePointers = [];
		$pointer = $usePointer + 1;

		while ($pointer < $endPointer) {
			$namePointer = TokenHelper::findNext($phpcsFile, TokenHelper::NAME_TOKEN_CODES, $pointer, $endPointer);

			if ($namePointer === null) {
				break;
			}

			$namePointers[] = $namePointer;
			$pointer = $namePointer + 1;
		}

		if (count($namePointers) <= 1) {
			return;
		}

		$names = [];

		foreach ($namePointers as $namePointer) {
			$names[] = $tokens[$namePointer]['content'];
		}

		$sortedNames = $names;

		usort($sortedNames, fn (string $a, string $b): int => $this->compare($a, $b));

		if ($names === $sortedNames) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Incorrect order of trait use statements.', $classPointer, self::CODE_INCORRECT_ORDER);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		foreach ($namePointers as $i => $namePointer) {
			$phpcsFile->fixer->replaceToken($namePointer, $sortedNames[$i]);
		}

		$phpcsFile->fixer->endChangeset();
	}

}
