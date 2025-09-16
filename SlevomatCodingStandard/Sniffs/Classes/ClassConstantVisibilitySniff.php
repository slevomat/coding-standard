<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_keys;
use function count;
use function in_array;
use function sprintf;
use const T_CONST;
use const T_EQUAL;
use const T_FINAL;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;

class ClassConstantVisibilitySniff implements Sniff
{

	public const CODE_MISSING_CONSTANT_VISIBILITY = 'MissingConstantVisibility';

	public bool $fixable = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CONST,
		];
	}

	public function process(File $phpcsFile, int $constantPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (count($tokens[$constantPointer]['conditions']) === 0) {
			return;
		}

		/** @var int $classPointer */
		$classPointer = array_keys($tokens[$constantPointer]['conditions'])[count($tokens[$constantPointer]['conditions']) - 1];
		if (!in_array($tokens[$classPointer]['code'], Tokens::$ooScopeTokens, true)) {
			return;
		}

		$visibilityPointer = TokenHelper::findPreviousEffective($phpcsFile, $constantPointer - 1);
		if ($tokens[$visibilityPointer]['code'] === T_FINAL) {
			$visibilityPointer = TokenHelper::findPreviousEffective($phpcsFile, $visibilityPointer - 1);
		}

		if (in_array($tokens[$visibilityPointer]['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)) {
			return;
		}

		$equalSignPointer = TokenHelper::findNext($phpcsFile, T_EQUAL, $constantPointer + 1);
		$namePointer = TokenHelper::findPreviousEffective($phpcsFile, $equalSignPointer - 1);

		$message = sprintf(
			'Constant %s::%s visibility missing.',
			ClassHelper::getFullyQualifiedName($phpcsFile, $classPointer),
			$tokens[$namePointer]['content'],
		);

		if ($this->fixable) {
			$fix = $phpcsFile->addFixableError($message, $constantPointer, self::CODE_MISSING_CONSTANT_VISIBILITY);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				FixerHelper::addBefore($phpcsFile, $constantPointer, 'public ');
				$phpcsFile->fixer->endChangeset();
			}
		} else {
			$phpcsFile->addError($message, $constantPointer, self::CODE_MISSING_CONSTANT_VISIBILITY);
		}
	}

}
