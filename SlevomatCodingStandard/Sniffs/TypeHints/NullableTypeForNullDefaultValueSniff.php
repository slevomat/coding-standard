<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_merge;
use function in_array;
use function sprintf;
use const T_BITWISE_AND;
use const T_ELLIPSIS;
use const T_EQUAL;
use const T_INLINE_THEN;
use const T_NULL;
use const T_NULLABLE;
use const T_VARIABLE;

class NullableTypeForNullDefaultValueSniff implements Sniff
{

	public const CODE_NULLABILITY_SYMBOL_REQUIRED = 'NullabilitySymbolRequired';

	private const NAME = 'SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::$functionTokenCodes;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $functionPointer
	 */
	public function process(File $phpcsFile, $functionPointer): void
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, self::NAME)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$startPointer = $tokens[$functionPointer]['parenthesis_opener'] + 1;
		$endPointer = $tokens[$functionPointer]['parenthesis_closer'];

		$typeHintTokenCodes = TokenHelper::getTypeHintTokenCodes();

		for ($i = $startPointer; $i < $endPointer; $i++) {
			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			$parameterName = $tokens[$i]['content'];

			$afterVariablePointer = TokenHelper::findNextEffective($phpcsFile, $i + 1);
			if ($tokens[$afterVariablePointer]['code'] !== T_EQUAL) {
				continue;
			}

			$afterEqualsPointer = TokenHelper::findNextEffective($phpcsFile, $afterVariablePointer + 1);
			if ($tokens[$afterEqualsPointer]['code'] !== T_NULL) {
				continue;
			}

			$ignoreTokensToFindTypeHint = array_merge(TokenHelper::$ineffectiveTokenCodes, [T_BITWISE_AND, T_ELLIPSIS]);
			$typeHintEndPointer = TokenHelper::findPreviousExcluding($phpcsFile, $ignoreTokensToFindTypeHint, $i - 1, $startPointer);

			if (
				$typeHintEndPointer === null
				|| !in_array($tokens[$typeHintEndPointer]['code'], $typeHintTokenCodes, true)
			) {
				continue;
			}

			$typeHintStartPointer = TypeHintHelper::getStartPointer($phpcsFile, $typeHintEndPointer);

			$pointerBeforeTypeHint = TokenHelper::findPreviousEffective(
				$phpcsFile,
				$typeHintStartPointer - 1,
				$tokens[$functionPointer]['parenthesis_opener']
			);

			// PHPCS reports T_NULLABLE as T_INLINE_THEN in PHP 8
			if ($pointerBeforeTypeHint !== null && in_array($tokens[$pointerBeforeTypeHint]['code'], [T_NULLABLE, T_INLINE_THEN], true)) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf('Parameter %s has null default value, but is not marked as nullable.', $parameterName),
				$i,
				self::CODE_NULLABILITY_SYMBOL_REQUIRED
			);

			if (!$fix) {
				continue;
			}

			$firstTypehint = TokenHelper::findNextEffective(
				$phpcsFile,
				$pointerBeforeTypeHint === null ? $startPointer : $pointerBeforeTypeHint + 1
			);

			$phpcsFile->fixer->beginChangeset();
			$phpcsFile->fixer->addContent($firstTypehint - 1, '?');
			$phpcsFile->fixer->endChangeset();
		}
	}

}
