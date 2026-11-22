<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IdentificatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function strtolower;
use const T_CLOSE_CURLY_BRACKET;
use const T_DOLLAR;
use const T_DOUBLE_COLON;
use const T_NULLSAFE_OBJECT_OPERATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_VARIABLE;

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

	public function process(File $phpcsFile, int $stringPointer): void
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

		$callableEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $objectOperator - 1);
		$callableStartPointer = null;
		$isFixable = true;

		if ($this->isPropertyOrConstant($phpcsFile, $callableEndPointer)) {
			$callableStartPointer = $this->findPropertyOrConstantStartPointer($phpcsFile, $callableEndPointer);
			$isFixable = $callableStartPointer !== null;
		}

		$errorMessage = 'Direct call of __invoke() is disallowed.';

		if (!$isFixable) {
			$phpcsFile->addError($errorMessage, $stringPointer, self::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);

			return;
		}

		$fix = $phpcsFile->addFixableError($errorMessage, $stringPointer, self::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($callableStartPointer !== null) {
			FixerHelper::addBefore($phpcsFile, $callableStartPointer, '(');
			FixerHelper::add($phpcsFile, $callableEndPointer, ')');
		}

		FixerHelper::removeBetweenIncluding($phpcsFile, $objectOperator, $parenthesisOpenerPointer - 1);

		$phpcsFile->fixer->endChangeset();
	}

	private function isPropertyOrConstant(File $phpcsFile, int $pointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		if (in_array($tokens[$pointer]['code'], TokenHelper::NAME_TOKEN_CODES, true)) {
			return true;
		}

		if ($tokens[$pointer]['code'] === T_CLOSE_CURLY_BRACKET) {
			$pointer = $tokens[$pointer]['bracket_opener'];
		} elseif ($tokens[$pointer]['code'] !== T_VARIABLE) {
			return false;
		}

		$pointerBefore = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 1);

		return in_array($tokens[$pointerBefore]['code'], [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON], true);
	}

	private function findPropertyOrConstantStartPointer(File $phpcsFile, int $endPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $startPointer */
		$startPointer = IdentificatorHelper::findStartPointer($phpcsFile, $endPointer);
		if (!in_array(
			$tokens[$startPointer]['code'],
			[T_VARIABLE, ...TokenHelper::NAME_TOKEN_CODES, ...TokenHelper::CLASS_KEYWORD_CODES],
			true,
		)) {
			return null;
		}

		$pointerBeforeStart = TokenHelper::findPreviousEffective($phpcsFile, $startPointer - 1);
		if (in_array(
			$tokens[$pointerBeforeStart]['code'],
			[T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_DOLLAR],
			true,
		)) {
			return null;
		}

		return $startPointer;
	}

}
