<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IdentificatorHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_keys;
use function count;
use function in_array;
use function range;
use function sprintf;
use function trim;
use const T_COALESCE;
use const T_ELSE;
use const T_ELSEIF;
use const T_EQUAL;
use const T_IF;
use const T_IS_IDENTICAL;
use const T_NULL;
use const T_SEMICOLON;

class RequireNullCoalesceEqualOperatorSniff implements Sniff
{

	public const CODE_REQUIRED_NULL_COALESCE_EQUAL_OPERATOR = 'RequiredNullCoalesceEqualOperator';

	public ?bool $enable = null;

	public bool $checkIfConditions = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_EQUAL,
		];
	}

	public function process(File $phpcsFile, int $equalPointer): void
	{
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 70400);

		if (!$this->enable) {
			return;
		}

		$this->checkCoalesce($phpcsFile, $equalPointer);
		$this->checkIf($phpcsFile, $equalPointer);
	}

	private function checkCoalesce(File $phpcsFile, int $equalPointer): void
	{
		/** @var int $variableStartPointer */
		$variableStartPointer = TokenHelper::findNextEffective($phpcsFile, $equalPointer + 1);
		$variableEndPointer = IdentificatorHelper::findEndPointer($phpcsFile, $variableStartPointer);

		if ($variableEndPointer === null) {
			return;
		}

		$nullCoalescePointer = TokenHelper::findNextEffective($phpcsFile, $variableEndPointer + 1);
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$nullCoalescePointer]['code'] !== T_COALESCE) {
			return;
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
		if (TokenHelper::findNext($phpcsFile, Tokens::OPERATORS, $nullCoalescePointer + 1, $semicolonPointer) !== null) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use "??=" operator instead of "=" and "??".',
			$equalPointer,
			self::CODE_REQUIRED_NULL_COALESCE_EQUAL_OPERATOR,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change($phpcsFile, $equalPointer, $nullCoalescePointer, '??=');

		$phpcsFile->fixer->endChangeset();
	}

	private function checkIf(File $phpcsFile, int $equalPointer): void
	{
		if (!$this->checkIfConditions) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$conditionsCount = count($tokens[$equalPointer]['conditions']);
		if ($conditionsCount === 0) {
			return;
		}

		$ifPointer = array_keys($tokens[$equalPointer]['conditions'])[$conditionsCount - 1];
		if ($tokens[$ifPointer]['code'] !== T_IF) {
			return;
		}

		$pointerAfterIfCondition = TokenHelper::findNextEffective($phpcsFile, $tokens[$ifPointer]['scope_closer'] + 1);
		if ($pointerAfterIfCondition !== null && in_array($tokens[$pointerAfterIfCondition]['code'], [T_ELSEIF, T_ELSE], true)) {
			return;
		}

		$ifVariableStartPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'] + 1);
		$ifVariableEndPointer = IdentificatorHelper::findEndPointer($phpcsFile, $ifVariableStartPointer);
		if ($ifVariableEndPointer === null) {
			return;
		}

		$nextIfPointer = TokenHelper::findNextEffective($phpcsFile, $ifVariableEndPointer + 1);
		if ($tokens[$nextIfPointer]['code'] !== T_IS_IDENTICAL) {
			return;
		}

		$nextIfPointer = TokenHelper::findNextEffective($phpcsFile, $nextIfPointer + 1);
		if ($tokens[$nextIfPointer]['code'] !== T_NULL) {
			return;
		}

		if (TokenHelper::findNextEffective($phpcsFile, $nextIfPointer + 1) !== $tokens[$ifPointer]['parenthesis_closer']) {
			return;
		}

		$beforeEqualVariableStartPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$ifPointer]['scope_opener'] + 1);
		$beforeEqualVariableEndPointer = IdentificatorHelper::findEndPointer($phpcsFile, $beforeEqualVariableStartPointer);
		if ($beforeEqualVariableEndPointer === null) {
			return;
		}

		if (TokenHelper::findNextEffective($phpcsFile, $beforeEqualVariableEndPointer + 1) !== $equalPointer) {
			return;
		}

		$variableName = IdentificatorHelper::getContent($phpcsFile, $ifVariableStartPointer, $ifVariableEndPointer);

		if ($variableName !== IdentificatorHelper::getContent(
			$phpcsFile,
			$beforeEqualVariableStartPointer,
			$beforeEqualVariableEndPointer,
		)) {
			return;
		}

		$semicolonPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $equalPointer + 1);
		if (TokenHelper::findNextEffective($phpcsFile, $semicolonPointer + 1) !== $tokens[$ifPointer]['scope_closer']) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use "??=" operator instead of if condition and "=".',
			$ifPointer,
			self::CODE_REQUIRED_NULL_COALESCE_EQUAL_OPERATOR,
		);

		if (!$fix) {
			return;
		}

		$codeStartPointer = TokenHelper::findNextEffective($phpcsFile, $equalPointer + 1);

		$afterNullCoalesceEqualCode = IndentationHelper::removeIndentation(
			$phpcsFile,
			range($codeStartPointer, $semicolonPointer),
			IndentationHelper::getIndentation($phpcsFile, $ifPointer),
		);

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::change(
			$phpcsFile,
			$ifPointer,
			$tokens[$ifPointer]['scope_closer'],
			sprintf('%s ??= %s', $variableName, trim($afterNullCoalesceEqualCode)),
		);
		$phpcsFile->fixer->endChangeset();
	}

}
