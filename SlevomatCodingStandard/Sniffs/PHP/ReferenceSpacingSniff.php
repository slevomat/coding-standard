<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IdentificatorHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function in_array;
use function sprintf;
use function str_repeat;
use function strlen;
use const T_AS;
use const T_BITWISE_AND;
use const T_COMMA;
use const T_DOUBLE_ARROW;
use const T_EQUAL;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_USE;

class ReferenceSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_SPACES_AFTER_REFERENCE = 'IncorrectSpacesAfterReference';

	public int $spacesCountAfterReference = 0;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_BITWISE_AND,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $referencePointer
	 */
	public function process(File $phpcsFile, $referencePointer): void
	{
		$this->spacesCountAfterReference = SniffSettingsHelper::normalizeInteger($this->spacesCountAfterReference);

		if (!$this->isReference($phpcsFile, $referencePointer)) {
			return;
		}

		$pointerAfterWhitespace = TokenHelper::findNextNonWhitespace($phpcsFile, $referencePointer + 1);

		$whitespace = TokenHelper::getContent($phpcsFile, $referencePointer + 1, $pointerAfterWhitespace - 1);

		$actualSpacesCount = strlen($whitespace);

		if ($this->spacesCountAfterReference === $actualSpacesCount) {
			return;
		}

		$errorMessage = $this->spacesCountAfterReference === 0
			? 'There must be no whitespace after reference.'
			: sprintf(
				'There must be exactly %d whitespace%s after reference.',
				$this->spacesCountAfterReference,
				$this->spacesCountAfterReference !== 1 ? 's' : '',
			);

		$fix = $phpcsFile->addFixableError($errorMessage, $referencePointer, self::CODE_INCORRECT_SPACES_AFTER_REFERENCE);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addContent($referencePointer, str_repeat(' ', $this->spacesCountAfterReference));

		FixerHelper::removeBetween($phpcsFile, $referencePointer, $pointerAfterWhitespace);

		$phpcsFile->fixer->endChangeset();
	}

	private function isReference(File $phpcsFile, int $referencePointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $referencePointer - 1);
		if (in_array($tokens[$previousPointer]['code'], TokenHelper::FUNCTION_TOKEN_CODES, true)) {
			return true;
		}

		$previousParenthesisOpenerPointer = TokenHelper::findPrevious($phpcsFile, T_OPEN_PARENTHESIS, $referencePointer - 1);
		if (
			$previousParenthesisOpenerPointer !== null
			&& $tokens[$previousParenthesisOpenerPointer]['parenthesis_closer'] > $referencePointer
		) {
			if (array_key_exists('parenthesis_owner', $tokens[$previousParenthesisOpenerPointer])) {
				$parenthesisOwnerPointer = $tokens[$previousParenthesisOpenerPointer]['parenthesis_owner'];
				if (in_array($tokens[$parenthesisOwnerPointer]['code'], TokenHelper::FUNCTION_TOKEN_CODES, true)) {
					return true;
				}
			}

			$pointerBeforeParenthesisOpener = TokenHelper::findPreviousEffective($phpcsFile, $previousParenthesisOpenerPointer - 1);
			if (
				$pointerBeforeParenthesisOpener !== null
				&& $tokens[$pointerBeforeParenthesisOpener]['code'] === T_USE
			) {
				return true;
			}
		}

		/** @var int $variableStartPointer */
		$variableStartPointer = TokenHelper::findNextEffective($phpcsFile, $referencePointer + 1);
		$variableEndPointer = IdentificatorHelper::findEndPointer($phpcsFile, $variableStartPointer);

		if ($variableEndPointer === null) {
			return false;
		}

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $referencePointer - 1);

		return in_array($tokens[$previousPointer]['code'], [T_EQUAL, T_DOUBLE_ARROW, T_OPEN_SHORT_ARRAY, T_COMMA, T_AS], true);
	}

}
