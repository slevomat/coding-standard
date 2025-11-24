<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_first;
use function array_keys;
use function array_map;
use function array_values;
use function count;
use function implode;
use function ksort;
use function uasort;
use const T_ABSTRACT;
use const T_CLASS;
use const T_FINAL;
use const T_READONLY;
use const T_WHITESPACE;

class ClassKeywordOrderSniff implements Sniff
{

	public const CODE_WRONG_CLASS_KEYWORD_ORDER = 'WrongClassKeywordOrder';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CLASS,
		];
	}

	public function process(File $phpcsFile, int $stackPtr): void
	{
		$tokens = $phpcsFile->getTokens();

		$modifierTokens = [
			T_ABSTRACT => 'abstract',
			T_FINAL => 'final',
			T_READONLY => 'readonly',
		];

		$foundModifiers = [];
		$currentIndex = TokenHelper::findPreviousEffective($phpcsFile, $stackPtr - 1);

		while ($currentIndex !== null && isset($modifierTokens[$tokens[$currentIndex]['code']])) {
			$foundModifiers[$currentIndex] = $tokens[$currentIndex]['code'];
			$currentIndex = TokenHelper::findPreviousEffective($phpcsFile, $currentIndex - 1);
		}

		if (count($foundModifiers) === 0) {
			return;
		}

		ksort($foundModifiers);

		$actualOrderCodes = array_values($foundModifiers);
		$actualOrderText = array_map(static fn ($code) => $modifierTokens[$code], $actualOrderCodes);

		$sortedModifiers = $foundModifiers;
		uasort($sortedModifiers, static function ($a, $b) {
			$priority = [
				T_ABSTRACT => 0,
				T_FINAL => 0,
				T_READONLY => 1,
			];
			return $priority[$a] <=> $priority[$b];
		});

		$expectedOrderCodes = array_values($sortedModifiers);
		$expectedOrderText = array_map(static fn ($code) => $modifierTokens[$code], $expectedOrderCodes);

		if ($actualOrderCodes === $expectedOrderCodes) {
			return;
		}

		$error = 'Class keywords are not in the correct order. Found: "%s class"; Expected: "%s class"';
		$data = [
			implode(' ', $actualOrderText),
			implode(' ', $expectedOrderText),
		];

		$fix = $phpcsFile->addFixableError($error, $stackPtr, self::CODE_WRONG_CLASS_KEYWORD_ORDER, $data);

		if ($fix !== true) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		foreach (array_keys($foundModifiers) as $ptr) {
			$phpcsFile->fixer->replaceToken($ptr, '');

			if ($tokens[$ptr + 1]['code'] === T_WHITESPACE) {
				$phpcsFile->fixer->replaceToken($ptr + 1, '');
			}
		}

		$firstModifierPtr = array_key_first($foundModifiers);

		$newContent = implode(' ', $expectedOrderText) . ' ';

		$phpcsFile->fixer->addContentBefore($firstModifierPtr, $newContent);

		$phpcsFile->fixer->endChangeset();
	}

}
