<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ReturnTypeHintSpacingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT = 'NoSpaceBetweenColonAndTypeHint';

	const CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT = 'MultipleSpacesBetweenColonAndTypeHint';

	const CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL = 'NoSpaceBetweenColonAndNullabilitySymbol';

	const CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL = 'MultipleSpacesBetweenColonAndNullabilitySymbol';

	const CODE_WHITESPACE_BEFORE_COLON = 'WhitespaceBeforeColon';

	const CODE_INCORRECT_SPACES_BEFORE_COLON = 'IncorrectWhitespaceBeforeColon';

	const CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL = 'WhitespaceAfterNullabilitySymbol';

	/** @var int */
	public $spacesCountBeforeColon = 0;

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_RETURN_TYPE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $typeHintPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $typeHintPointer)
	{
		$tokens = $phpcsFile->getTokens();

		$typeHintPointer = TokenHelper::findPreviousExcluding($phpcsFile, [T_NS_SEPARATOR, T_STRING], $typeHintPointer - 1) + 1;
		$colonPointer = TokenHelper::findPreviousExcluding($phpcsFile, array_merge([T_NULLABLE], TokenHelper::$ineffectiveTokenCodes), $typeHintPointer - 1);

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $colonPointer + 1);
		$nullabilitySymbolPointer = $nextPointer !== null && $tokens[$nextPointer]['code'] === T_NULLABLE ? $nextPointer : null;

		if ($nullabilitySymbolPointer === null) {
			if ($tokens[$colonPointer + 1]['code'] !== T_WHITESPACE) {
				$fix = $phpcsFile->addFixableError('There must be exactly one space between return type hint colon and return type hint.', $typeHintPointer, self::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContent($colonPointer, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			} elseif ($tokens[$colonPointer + 1]['content'] !== ' ') {
				$fix = $phpcsFile->addFixableError('There must be exactly one space between return type hint colon and return type hint.', $typeHintPointer, self::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($colonPointer + 1, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			}
		} else {
			if ($tokens[$colonPointer + 1]['code'] !== T_WHITESPACE) {
				$fix = $phpcsFile->addFixableError('There must be exactly one space between return type hint colon and return type hint nullability symbol.', $typeHintPointer, self::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContent($colonPointer, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			} elseif ($tokens[$colonPointer + 1]['content'] !== ' ') {
				$fix = $phpcsFile->addFixableError('There must be exactly one space between return type hint colon and return type hint nullability symbol.', $typeHintPointer, self::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($colonPointer + 1, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			}

			if ($nullabilitySymbolPointer + 1 !== $typeHintPointer) {
				$fix = $phpcsFile->addFixableError('There must be no whitespace between return type hint nullability symbol and return type hint.', $typeHintPointer, self::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($nullabilitySymbolPointer + 1, '');
					$phpcsFile->fixer->endChangeset();
				}
			}
		}

		$spacesCountBeforeColon = SniffSettingsHelper::normalizeInteger($this->spacesCountBeforeColon);
		$expectedSpaces = str_repeat(' ', $spacesCountBeforeColon);

		if ($tokens[$colonPointer - 1]['code'] !== T_CLOSE_PARENTHESIS && $tokens[$colonPointer - 1]['content'] !== $expectedSpaces) {
			if ($spacesCountBeforeColon === 0) {
				$fix = $phpcsFile->addFixableError('There must be no whitespace between closing parenthesis and return type colon.', $typeHintPointer, self::CODE_WHITESPACE_BEFORE_COLON);
			} else {
				$fix = $phpcsFile->addFixableError(
					sprintf('There must be exactly %d whitespace%s between closing parenthesis and return type colon.', $spacesCountBeforeColon, $spacesCountBeforeColon !== 1 ? 's' : ''),
					$typeHintPointer,
					self::CODE_INCORRECT_SPACES_BEFORE_COLON
				);
			}
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($colonPointer - 1, $expectedSpaces);
				$phpcsFile->fixer->endChangeset();
			}
		} elseif ($tokens[$colonPointer - 1]['code'] === T_CLOSE_PARENTHESIS && $spacesCountBeforeColon !== 0) {
			$fix = $phpcsFile->addFixableError(
				sprintf('There must be exactly %d whitespace%s between closing parenthesis and return type colon.', $spacesCountBeforeColon, $spacesCountBeforeColon !== 1 ? 's' : ''),
				$typeHintPointer,
				self::CODE_INCORRECT_SPACES_BEFORE_COLON
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($colonPointer - 1, $expectedSpaces);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
