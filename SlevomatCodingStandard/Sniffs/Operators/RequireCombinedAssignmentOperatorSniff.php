<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IdentificatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function in_array;
use function sprintf;
use const T_BITWISE_AND;
use const T_BITWISE_OR;
use const T_BITWISE_XOR;
use const T_CLOSE_SQUARE_BRACKET;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_DIVIDE;
use const T_DNUMBER;
use const T_DOUBLE_QUOTED_STRING;
use const T_EQUAL;
use const T_LNUMBER;
use const T_MINUS;
use const T_MODULUS;
use const T_MULTIPLY;
use const T_PLUS;
use const T_POW;
use const T_SEMICOLON;
use const T_SL;
use const T_SR;
use const T_START_HEREDOC;
use const T_START_NOWDOC;
use const T_STRING_CONCAT;

class RequireCombinedAssignmentOperatorSniff implements Sniff
{

	public const CODE_REQUIRED_COMBINED_ASSIGNMENT_OPERATOR = 'RequiredCombinedAssignmentOperator';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_EQUAL,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $equalPointer
	 */
	public function process(File $phpcsFile, $equalPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $variableStartPointer */
		$variableStartPointer = TokenHelper::findNextEffective($phpcsFile, $equalPointer + 1);
		$variableEndPointer = IdentificatorHelper::findEndPointer($phpcsFile, $variableStartPointer);

		if ($variableEndPointer === null) {
			return;
		}

		$operatorPointer = TokenHelper::findNextEffective($phpcsFile, $variableEndPointer + 1);

		$operators = [
			T_BITWISE_AND => '&=',
			T_BITWISE_OR => '|=',
			T_STRING_CONCAT => '.=',
			T_DIVIDE => '/=',
			T_MINUS => '-=',
			T_POW => '**=',
			T_MODULUS => '%=',
			T_MULTIPLY => '*=',
			T_PLUS => '+=',
			T_SL => '<<=',
			T_SR => '>>=',
			T_BITWISE_XOR => '^=',
		];

		if (!array_key_exists($tokens[$operatorPointer]['code'], $operators)) {
			return;
		}

		$isFixable = true;

		if ($tokens[$variableEndPointer]['code'] === T_CLOSE_SQUARE_BRACKET) {
			$pointerAfterOperator = TokenHelper::findNextEffective($phpcsFile, $operatorPointer + 1);
			if (in_array(
				$tokens[$pointerAfterOperator]['code'],
				[T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING, T_START_HEREDOC, T_START_NOWDOC],
				true
			)) {
				return;
			}

			$isFixable = in_array($tokens[$pointerAfterOperator]['code'], [T_LNUMBER, T_DNUMBER], true);
		}

		$variableContent = IdentificatorHelper::getContent($phpcsFile, $variableStartPointer, $variableEndPointer);

		/** @var int $beforeEqualEndPointer */
		$beforeEqualEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $equalPointer - 1);
		$beforeEqualStartPointer = IdentificatorHelper::findStartPointer($phpcsFile, $beforeEqualEndPointer);

		if ($beforeEqualStartPointer === null) {
			return;
		}

		$beforeEqualVariableContent = IdentificatorHelper::getContent($phpcsFile, $beforeEqualStartPointer, $beforeEqualEndPointer);

		if ($beforeEqualVariableContent !== $variableContent) {
			return;
		}

		$semicolonPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $equalPointer + 1);
		if (TokenHelper::findNext($phpcsFile, Tokens::$operators, $operatorPointer + 1, $semicolonPointer) !== null) {
			return;
		}

		$errorMessage = sprintf(
			'Use "%s" operator instead of "=" and "%s".',
			$operators[$tokens[$operatorPointer]['code']],
			$tokens[$operatorPointer]['content']
		);

		if (!$isFixable) {
			$phpcsFile->addError($errorMessage, $equalPointer, self::CODE_REQUIRED_COMBINED_ASSIGNMENT_OPERATOR);

			return;
		}

		$fix = $phpcsFile->addFixableError($errorMessage, $equalPointer, self::CODE_REQUIRED_COMBINED_ASSIGNMENT_OPERATOR);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($equalPointer, $operators[$tokens[$operatorPointer]['code']]);
		FixerHelper::removeBetweenIncluding($phpcsFile, $equalPointer + 1, $operatorPointer);
		$phpcsFile->fixer->endChangeset();
	}

}
