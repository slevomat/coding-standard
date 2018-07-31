<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_BITWISE_AND;
use const T_BITWISE_OR;
use const T_BITWISE_XOR;
use const T_DIVIDE;
use const T_DOLLAR;
use const T_DOUBLE_COLON;
use const T_EQUAL;
use const T_MINUS;
use const T_MODULUS;
use const T_MULTIPLY;
use const T_OBJECT_OPERATOR;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_SQUARE_BRACKET;
use const T_PARENT;
use const T_PLUS;
use const T_POW;
use const T_SELF;
use const T_SL;
use const T_SR;
use const T_STATIC;
use const T_STRING;
use const T_STRING_CONCAT;
use const T_VARIABLE;
use function array_key_exists;
use function in_array;
use function sprintf;
use function strlen;

class RequireCombinedAssignmentOperatorSniff implements Sniff
{

	public const CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR = 'RequiredCombinedAssigmentOperator';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_EQUAL,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $equalPointer
	 */
	public function process(File $phpcsFile, $equalPointer): void
	{
		/** @var int $variableStartPointer */
		$variableStartPointer = TokenHelper::findNextEffective($phpcsFile, $equalPointer + 1);
		$variableEndPointer = $this->findVariableEndPointer($phpcsFile, $variableStartPointer);

		if ($variableEndPointer === null) {
			return;
		}

		$operatorPointer = TokenHelper::findNextEffective($phpcsFile, $variableEndPointer + 1);
		$tokens = $phpcsFile->getTokens();

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

		$variableContent = '';
		for ($i = $variableStartPointer; $i <= $variableEndPointer; $i++) {
			if (in_array($tokens[$i]['code'], TokenHelper::$ineffectiveTokenCodes, true)) {
				continue;
			}

			$variableContent .= $tokens[$i]['content'];
		}

		$beforeEqualVariableContent = '';
		for ($i = $equalPointer - 1; $i >= 0; $i--) {
			if (in_array($tokens[$i]['code'], TokenHelper::$ineffectiveTokenCodes, true)) {
				continue;
			}

			$beforeEqualVariableContent = $tokens[$i]['content'] . $beforeEqualVariableContent;

			if (strlen($beforeEqualVariableContent) >= strlen($variableContent)) {
				break;
			}
		}

		if ($beforeEqualVariableContent !== $variableContent) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('Use "%s" operator instead of "=" and "%s".', $operators[$tokens[$operatorPointer]['code']], $tokens[$operatorPointer]['content']),
			$equalPointer,
			self::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($equalPointer, $operators[$tokens[$operatorPointer]['code']]);
		for ($i = $equalPointer + 1; $i <= $operatorPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function findVariableEndPointer(File $phpcsFile, int $startPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		if (in_array($tokens[$startPointer]['code'], TokenHelper::$nameTokenCodes, true)) {
			$startPointer = TokenHelper::findNextExcluding($phpcsFile, TokenHelper::$nameTokenCodes, $startPointer + 1) - 1;
		}

		/** @var int $nextPointer */
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $startPointer + 1);

		if (
			in_array($tokens[$startPointer]['code'], [T_STRING, T_SELF, T_STATIC, T_PARENT], true)
			&& $tokens[$nextPointer]['code'] === T_DOUBLE_COLON
		) {
			return $this->getVariableEndPointerAfterOperator($phpcsFile, $nextPointer);
		}

		if ($tokens[$startPointer]['code'] === T_VARIABLE) {
			if (in_array($tokens[$nextPointer]['code'], [T_DOUBLE_COLON, T_OBJECT_OPERATOR], true)) {
				return $this->getVariableEndPointerAfterOperator($phpcsFile, $nextPointer);
			}

			return $startPointer;
		}

		return null;
	}

	private function getVariableEndPointerAfterOperator(File $phpcsFile, int $operatorPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $nextPointer */
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $operatorPointer + 1);

		if ($tokens[$nextPointer]['code'] === T_DOLLAR) {
			/** @var int $nextPointer */
			$nextPointer = TokenHelper::findNextEffective($phpcsFile, $nextPointer + 1);
		}

		if ($tokens[$nextPointer]['code'] === T_OPEN_CURLY_BRACKET) {
			return $this->getVariableEndPointerAfterVariablePart($phpcsFile, $tokens[$nextPointer]['bracket_closer']);
		}

		return $this->getVariableEndPointerAfterVariablePart($phpcsFile, $nextPointer);
	}

	private function getVariableEndPointerAfterVariablePart(File $phpcsFile, int $variablePartPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $nextPointer */
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $variablePartPointer + 1);

		if (in_array($tokens[$nextPointer]['code'], [T_OBJECT_OPERATOR, T_DOUBLE_COLON], true)) {
			return $this->getVariableEndPointerAfterOperator($phpcsFile, $nextPointer);
		}

		if ($tokens[$nextPointer]['code'] === T_OPEN_SQUARE_BRACKET) {
			return $this->getVariableEndPointerAfterVariablePart($phpcsFile, $tokens[$nextPointer]['bracket_closer']);
		}

		return $variablePartPointer;
	}

}
