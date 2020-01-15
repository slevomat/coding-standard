<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function strlen;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_CLOSE_SQUARE_BRACKET;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_DNUMBER;
use const T_ENCAPSED_AND_WHITESPACE;
use const T_LNUMBER;
use const T_MINUS;
use const T_NUM_STRING;
use const T_STRING;
use const T_VARIABLE;
use const T_WHITESPACE;

final class NegationOperatorSpacingSniff implements Sniff
{

	public const CODE_INVALID_SPACE_AFTER_MINUS = 'InvalidSpaceAfterMinus';

	/** @var int */
	public $spacesCount = 0;

	/**
	 * {@inheritdoc}
	 */
	public function register(): array
	{
		return [T_MINUS];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $pointer
	 */
	public function process(File $file, $pointer): void
	{
		$tokens = $file->getTokens();

		$previousEffective = TokenHelper::findPreviousEffective($file, $pointer - 1);

		$possibleOperandTypes = [
			T_CONSTANT_ENCAPSED_STRING,
			T_CLOSE_PARENTHESIS,
			T_CLOSE_SHORT_ARRAY,
			T_CLOSE_SQUARE_BRACKET,
			T_DNUMBER,
			T_ENCAPSED_AND_WHITESPACE,
			T_LNUMBER,
			T_NUM_STRING,
			T_STRING,
			T_VARIABLE,
		];

		if (in_array($tokens[$previousEffective]['code'], $possibleOperandTypes, true)) {
			return;
		}

		$whitespacePointer = $pointer + 1;

		$numberOfSpaces = $tokens[$whitespacePointer]['code'] !== T_WHITESPACE ? 0 : strlen($tokens[$whitespacePointer]['content']);
		if ($numberOfSpaces === $this->spacesCount) {
			return;
		}

		$fix = $file->addFixableError(
			'Expected exactly %d space after "%s"; %d found',
			$pointer,
			self::CODE_INVALID_SPACE_AFTER_MINUS,
			[$this->spacesCount, $tokens[$pointer]['content'], $numberOfSpaces]
		);

		if (! $fix) {
			return;
		}

		if ($this->spacesCount > $numberOfSpaces) {
			$file->fixer->addContent($pointer, ' ');

			return;
		}

		$file->fixer->replaceToken($whitespacePointer, '');
	}

}
