<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_CLASS_C;
use const T_DOUBLE_COLON;
use const T_NS_SEPARATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_VARIABLE;
use function in_array;
use function sprintf;
use function strtolower;

class ModernClassNameReferenceSniff implements Sniff
{

	public const CODE_CLASS_NAME_REFERENCED_VIA_MAGIC_CONSTANT = 'ClassNameReferencedViaMagicConstant';
	public const CODE_CLASS_NAME_REFERENCED_VIA_FUNCTION_CALL = 'ClassNameReferencedViaFunctionCall';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_CLASS_C,
			T_STRING,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] === T_CLASS_C) {
			$this->checkMagicConstant($phpcsFile, $pointer);
			return;
		}

		$this->checkFunctionCall($phpcsFile, $pointer);
	}

	private function checkMagicConstant(File $phpcsFile, int $pointer): void
	{
		$fix = $phpcsFile->addFixableError('Class name referenced via magic constant.', $pointer, self::CODE_CLASS_NAME_REFERENCED_VIA_MAGIC_CONSTANT);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($pointer, 'self::class');
		$phpcsFile->fixer->endChangeset();
	}

	private function checkFunctionCall(File $phpcsFile, int $pointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$functionName = strtolower($tokens[$pointer]['content']);

		$functionNames = [
			'get_class',
			'get_parent_class',
			'get_called_class',
		];

		if (!in_array($functionName, $functionNames, true)) {
			return;
		}

		$openParenthesisPointer = TokenHelper::findNextEffective($phpcsFile, $pointer + 1);
		if ($tokens[$openParenthesisPointer]['code'] !== T_OPEN_PARENTHESIS) {
			return;
		}

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 1);
		if (in_array($tokens[$previousPointer]['code'], [T_OBJECT_OPERATOR, T_DOUBLE_COLON], true)) {
			return;
		}

		if ($functionName === 'get_class') {
			$parameterPointer = TokenHelper::findNextEffective($phpcsFile, $openParenthesisPointer + 1, $tokens[$openParenthesisPointer]['parenthesis_closer']);

			if ($parameterPointer === null) {
				$fixedContent = 'self::class';
			} elseif ($tokens[$parameterPointer]['code'] !== T_VARIABLE) {
				return;
			} else {
				$parameterName = strtolower($tokens[$parameterPointer]['content']);
				if ($parameterName !== '$this') {
					return;
				}

				$pointerAfterParameterPointer = TokenHelper::findNextEffective($phpcsFile, $parameterPointer + 1);
				if ($pointerAfterParameterPointer !== $tokens[$openParenthesisPointer]['parenthesis_closer']) {
					return;
				}

				$fixedContent = 'static::class';
			}

		} elseif ($functionName === 'get_parent_class') {
			$fixedContent = 'parent::class';
		} else {
			$fixedContent = 'static::class';
		}

		$fix = $phpcsFile->addFixableError(sprintf('Class name referenced via call of function %s().', $functionName), $pointer, self::CODE_CLASS_NAME_REFERENCED_VIA_FUNCTION_CALL);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		if ($tokens[$pointer - 1]['code'] === T_NS_SEPARATOR) {
			$phpcsFile->fixer->replaceToken($pointer - 1, '');
		}
		$phpcsFile->fixer->replaceToken($pointer, $fixedContent);
		for ($i = $pointer + 1; $i <= $tokens[$openParenthesisPointer]['parenthesis_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

}
