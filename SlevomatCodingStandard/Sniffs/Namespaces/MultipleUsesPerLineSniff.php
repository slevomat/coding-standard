<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class MultipleUsesPerLineSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_MULTIPLE_USES_PER_LINE = 'MultipleUsesPerLine';

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

		$endPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $usePointer + 1);
		$commaPointer = TokenHelper::findNext($phpcsFile, T_COMMA, $usePointer + 1, $endPointer);

		if ($commaPointer === null) {
			return;
		}

		$phpcsFile->addError(
			'Multiple used types per use statement are forbidden.',
			$commaPointer,
			self::CODE_MULTIPLE_USES_PER_LINE
		);
	}

}
