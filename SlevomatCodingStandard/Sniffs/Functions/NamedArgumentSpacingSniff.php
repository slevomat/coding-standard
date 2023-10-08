<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use const T_COLON;
use const T_PARAM_NAME;
use const T_WHITESPACE;

class NamedArgumentSpacingSniff implements Sniff
{

	public const CODE_WHITESPACE_BEFORE_COLON = 'WhitespaceBeforeColon';
	public const CODE_NO_WHITESPACE_AFTER_COLON = 'NoWhitespaceAfterColon';

	/**
	 * @return list<string>
	 */
	public function register(): array
	{
		return [
			T_PARAM_NAME,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $colonPointer */
		$colonPointer = TokenHelper::findNext($phpcsFile, T_COLON, $pointer + 1);

		$parameterName = $tokens[$pointer]['content'];

		if ($colonPointer !== $pointer + 1) {
			$fix = $phpcsFile->addFixableError(
				sprintf('There must be no whitespace between named argument "%s" and colon.', $parameterName),
				$colonPointer,
				self::CODE_WHITESPACE_BEFORE_COLON
			);
			if ($fix) {
				$phpcsFile->fixer->replaceToken($colonPointer - 1, '');
			}
		}

		$whitespacePointer = $colonPointer + 1;

		if (
			$tokens[$whitespacePointer]['code'] === T_WHITESPACE
			&& $tokens[$whitespacePointer]['content'] === ' '
		) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('There must be exactly one space after colon in named argument "%s".', $parameterName),
			$colonPointer,
			self::CODE_NO_WHITESPACE_AFTER_COLON
		);

		if (!$fix) {
			return;
		}

		if ($tokens[$whitespacePointer]['code'] === T_WHITESPACE) {
			$phpcsFile->fixer->replaceToken($whitespacePointer, ' ');
		} else {
			$phpcsFile->fixer->addContent($colonPointer, ' ');
		}
	}

}
