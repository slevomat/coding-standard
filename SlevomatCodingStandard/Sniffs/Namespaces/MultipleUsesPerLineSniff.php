<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\UseStatementHelper;

class MultipleUsesPerLineSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_MULTIPLE_USES_PER_LINE = 'MultipleUsesPerLine';

	/**
	 * @return int[]
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
	 * @return int|null
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $usePointer)
	{
		if (
			UseStatementHelper::isAnonymousFunctionUse($phpcsFile, $usePointer)
			|| UseStatementHelper::isTraitUse($phpcsFile, $usePointer)
		) {
			return $usePointer + 1;
		}

		$endPointer = $phpcsFile->findNext(T_SEMICOLON, $usePointer + 1);
		$commaPointer = $phpcsFile->findNext(T_COMMA, $usePointer + 1, $endPointer);

		if ($commaPointer !== false) {
			$phpcsFile->addError(
				'Multiple used types per use statement are forbidden.',
				$commaPointer,
				self::CODE_MULTIPLE_USES_PER_LINE
			);
		}
	}

}
