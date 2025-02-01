<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function strtolower;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_STRING;

class DisallowDirectMagicInvokeCallSniff implements Sniff
{

	public const CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL = 'DisallowDirectMagicInvokeCall';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_STRING,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stringPointer
	 */
	public function process(File $phpcsFile, $stringPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$parenthesisOpenerPointer = TokenHelper::findNextEffective($phpcsFile, $stringPointer + 1);
		if ($tokens[$parenthesisOpenerPointer]['code'] !== T_OPEN_PARENTHESIS) {
			return;
		}

		if (strtolower($tokens[$stringPointer]['content']) !== '__invoke') {
			return;
		}

		$objectOperator = TokenHelper::findPreviousEffective($phpcsFile, $stringPointer - 1);
		if ($tokens[$objectOperator]['code'] !== T_OBJECT_OPERATOR) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Direct call of __invoke() is disallowed.',
			$stringPointer,
			self::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL,
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetweenIncluding($phpcsFile, $objectOperator, $parenthesisOpenerPointer - 1);

		$phpcsFile->fixer->endChangeset();
	}

}
