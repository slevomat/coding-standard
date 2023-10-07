<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IdentificatorHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TernaryOperatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function count;
use function in_array;
use function min;
use function preg_split;
use function sprintf;
use function strtolower;
use function substr_count;
use function trim;
use const PREG_SPLIT_DELIM_CAPTURE;
use const T_BOOLEAN_AND;
use const T_BOOLEAN_OR;
use const T_CLOSE_PARENTHESIS;
use const T_DOUBLE_COLON;
use const T_INLINE_THEN;
use const T_IS_IDENTICAL;
use const T_IS_NOT_IDENTICAL;
use const T_NULL;
use const T_NULLSAFE_OBJECT_OPERATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_PARENTHESIS;
use const T_SEMICOLON;
use const T_STRING;

class RequireNullSafeObjectOperatorSniff implements Sniff
{

	public const CODE_REQUIRED_NULL_SAFE_OBJECT_OPERATOR = 'RequiredNullSafeObjectOperator';

	private const OPERATOR_REGEXP = '~(::|->|\?->)~';

	/** @var bool|null */
	public $enable = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_IS_IDENTICAL,
			T_IS_NOT_IDENTICAL,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $identicalPointer
	 */
	public function process(File $phpcsFile, $identicalPointer): int
	{
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 80000);

		if (!$this->enable) {
			return $identicalPointer + 1;
		}

		$tokens = $phpcsFile->getTokens();

		[$pointerBeforeIdentical, $pointerAfterIdentical] = $this->getIdenticalData($phpcsFile, $identicalPointer);

		if ($tokens[$pointerBeforeIdentical]['code'] !== T_NULL && $tokens[$pointerAfterIdentical]['code'] !== T_NULL) {
			return $identicalPointer + 1;
		}

		[$identificatorStartPointer, $identificatorEndPointer, $conditionStartPointer] = $this->getConditionData(
			$phpcsFile,
			$pointerBeforeIdentical,
			$pointerAfterIdentical
		);

		if ($identificatorStartPointer === null || $identificatorEndPointer === null) {
			return $identicalPointer + 1;
		}

		$isYoda = $tokens[$pointerBeforeIdentical]['code'] === T_NULL;
		$identificator = IdentificatorHelper::getContent($phpcsFile, $identificatorStartPointer, $identificatorEndPointer);

		$pointerAfterCondition = TokenHelper::findNextEffective(
			$phpcsFile,
			($isYoda ? $identificatorEndPointer : $pointerAfterIdentical) + 1
		);

		$allowedBooleanCondition = $tokens[$identicalPointer]['code'] === T_IS_NOT_IDENTICAL ? T_BOOLEAN_AND : T_BOOLEAN_OR;
		if ($tokens[$pointerAfterCondition]['code'] === $allowedBooleanCondition) {
			return $this->checkNextCondition($phpcsFile, $identicalPointer, $conditionStartPointer, $identificator, $pointerAfterCondition);
		}

		if ($tokens[$pointerAfterCondition]['code'] === T_INLINE_THEN) {
			$this->checkTernaryOperator($phpcsFile, $identicalPointer, $conditionStartPointer, $identificator, $pointerAfterCondition);
			return $pointerAfterCondition + 1;
		}

		return $identicalPointer + 1;
	}

	private function checkTernaryOperator(
		File $phpcsFile,
		int $identicalPointer,
		int $conditionStartPointer,
		string $identificator,
		int $inlineThenPointer
	): void
	{
		$tokens = $phpcsFile->getTokens();

		$ternaryOperatorStartPointer = TernaryOperatorHelper::getStartPointer($phpcsFile, $inlineThenPointer);

		$searchStartPointer = $ternaryOperatorStartPointer;
		do {
			$booleanOperatorPointer = TokenHelper::findNext($phpcsFile, Tokens::$booleanOperators, $searchStartPointer, $inlineThenPointer);
			if ($booleanOperatorPointer === null) {
				break;
			}

			$identicalPointer = TokenHelper::findNext(
				$phpcsFile,
				[T_IS_IDENTICAL, T_IS_NOT_IDENTICAL],
				$searchStartPointer,
				$booleanOperatorPointer
			);

			if ($identicalPointer === null) {
				return;
			}

			$pointerAfterIdentical = TokenHelper::findNextEffective($phpcsFile, $identicalPointer + 1);

			if ($tokens[$pointerAfterIdentical]['code'] !== T_NULL) {
				return;
			}

			$searchStartPointer = $booleanOperatorPointer + 1;

		} while (true);

		$pointerBeforeCondition = TokenHelper::findPreviousEffective($phpcsFile, $conditionStartPointer - 1);
		if (in_array($tokens[$pointerBeforeCondition]['code'], [T_BOOLEAN_AND, T_BOOLEAN_OR], true)) {
			$previousIdenticalPointer = TokenHelper::findPreviousLocal(
				$phpcsFile,
				[T_IS_IDENTICAL, T_IS_NOT_IDENTICAL],
				$pointerBeforeCondition
			);

			if ($previousIdenticalPointer !== null) {
				[$pointerBeforePreviousIdentical, $pointerAfterPreviousIdentical] = $this->getIdenticalData(
					$phpcsFile,
					$previousIdenticalPointer
				);

				[$previousIdentificatorStartPointer, $previousIdentificatorEndPointer] = $this->getConditionData(
					$phpcsFile,
					$pointerBeforePreviousIdentical,
					$pointerAfterPreviousIdentical
				);

				if ($previousIdentificatorStartPointer !== null && $previousIdentificatorEndPointer !== null) {
					$previousIdentificator = IdentificatorHelper::getContent(
						$phpcsFile,
						$previousIdentificatorStartPointer,
						$previousIdentificatorEndPointer
					);

					if (!self::areIdentificatorsCompatible($previousIdentificator, $identificator)) {
						return;
					}
				}
			}
		}

		$defaultInElse = $tokens[$identicalPointer]['code'] === T_IS_NOT_IDENTICAL;
		$inlineElsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $inlineThenPointer);
		$inlineElseEndPointer = TernaryOperatorHelper::getEndPointer($phpcsFile, $inlineThenPointer, $inlineElsePointer);

		if ($defaultInElse) {
			$nextIdentificatorPointers = $this->getNextIdentificator($phpcsFile, $inlineThenPointer);

			if ($nextIdentificatorPointers === null) {
				return;
			}

			[$nextIdentificatorStartPointer, $nextIdentificatorEndPointer] = $nextIdentificatorPointers;

			$nextIdentificator = IdentificatorHelper::getContent($phpcsFile, $nextIdentificatorStartPointer, $nextIdentificatorEndPointer);

			if (!$this->areIdentificatorsCompatible($identificator, $nextIdentificator)) {
				return;
			}

			if (TokenHelper::findNextEffective($phpcsFile, $nextIdentificatorEndPointer + 1) !== $inlineElsePointer) {
				return;
			}

			$identificatorDifference = $this->getIdentificatorDifference(
				$phpcsFile,
				$identificator,
				$nextIdentificatorStartPointer,
				$nextIdentificatorEndPointer
			);

			$firstPointerInElse = TokenHelper::findNextEffective($phpcsFile, $inlineElsePointer + 1);

			$defaultContent = TokenHelper::getContent($phpcsFile, $firstPointerInElse, $inlineElseEndPointer);

			$conditionEndPointer = $inlineElseEndPointer;

		} else {
			$nullPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);

			if ($tokens[$nullPointer]['code'] !== T_NULL) {
				return;
			}

			if (TokenHelper::findNextEffective($phpcsFile, $nullPointer + 1) !== $inlineElsePointer) {
				return;
			}

			$nextIdentificatorPointers = $this->getNextIdentificator($phpcsFile, $inlineElsePointer);

			if ($nextIdentificatorPointers === null) {
				return;
			}

			[$nextIdentificatorStartPointer, $nextIdentificatorEndPointer] = $nextIdentificatorPointers;

			if ($nextIdentificatorEndPointer !== $inlineElseEndPointer) {
				return;
			}

			$nextIdentificator = IdentificatorHelper::getContent($phpcsFile, $nextIdentificatorStartPointer, $nextIdentificatorEndPointer);

			if (!$this->areIdentificatorsCompatible($identificator, $nextIdentificator)) {
				return;
			}

			$identificatorDifference = $this->getIdentificatorDifference(
				$phpcsFile,
				$identificator,
				$nextIdentificatorStartPointer,
				$nextIdentificatorEndPointer
			);

			$defaultContent = trim(TokenHelper::getContent($phpcsFile, $inlineThenPointer + 1, $inlineElsePointer - 1));

			$conditionEndPointer = $nextIdentificatorEndPointer;
		}

		$fix = $phpcsFile->addFixableError('Operator ?-> is required.', $identicalPointer, self::CODE_REQUIRED_NULL_SAFE_OBJECT_OPERATOR);

		if (!$fix) {
			return;
		}

		$conditionContent = sprintf('%s?%s', $identificator, $identificatorDifference);
		if (strtolower($defaultContent) !== 'null') {
			$conditionContent .= sprintf(' ?? %s', $defaultContent);
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change($phpcsFile, $conditionStartPointer, $conditionEndPointer, $conditionContent);

		$phpcsFile->fixer->endChangeset();
	}

	private function checkNextCondition(
		File $phpcsFile,
		int $identicalPointer,
		int $conditionStartPointer,
		string $identificator,
		int $nextConditionBooleanPointer
	): int
	{
		$nextIdentificatorPointers = $this->getNextIdentificator($phpcsFile, $nextConditionBooleanPointer);

		if ($nextIdentificatorPointers === null) {
			return $nextConditionBooleanPointer;
		}

		[$nextIdentificatorStartPointer, $nextIdentificatorEndPointer] = $nextIdentificatorPointers;

		$nextIdentificator = IdentificatorHelper::getContent($phpcsFile, $nextIdentificatorStartPointer, $nextIdentificatorEndPointer);

		if (!$this->areIdentificatorsCompatible($identificator, $nextIdentificator)) {
			return $nextIdentificatorEndPointer;
		}

		$pointerAfterNexIdentificator = TokenHelper::findNextEffective($phpcsFile, $nextIdentificatorEndPointer + 1);

		$tokens = $phpcsFile->getTokens();

		if (
			$tokens[$pointerAfterNexIdentificator]['code'] !== $tokens[$identicalPointer]['code']
			&& !in_array($tokens[$pointerAfterNexIdentificator]['code'], [T_INLINE_THEN, T_SEMICOLON], true)
		) {
			return $pointerAfterNexIdentificator;
		}

		if (!in_array($tokens[$pointerAfterNexIdentificator]['code'], [T_IS_IDENTICAL, T_IS_NOT_IDENTICAL], true)) {
			return $pointerAfterNexIdentificator;
		}

		$pointerAfterIdentical = TokenHelper::findNextEffective($phpcsFile, $pointerAfterNexIdentificator + 1);
		if ($tokens[$pointerAfterIdentical]['code'] !== T_NULL) {
			return $pointerAfterNexIdentificator;
		}

		$identificatorDifference = $this->getIdentificatorDifference(
			$phpcsFile,
			$identificator,
			$nextIdentificatorStartPointer,
			$nextIdentificatorEndPointer
		);

		$fix = $phpcsFile->addFixableError('Operator ?-> is required.', $identicalPointer, self::CODE_REQUIRED_NULL_SAFE_OBJECT_OPERATOR);

		if (!$fix) {
			return $pointerAfterNexIdentificator;
		}

		$isConditionOfTernaryOperator = TernaryOperatorHelper::isConditionOfTernaryOperator($phpcsFile, $identicalPointer);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change(
			$phpcsFile,
			$conditionStartPointer,
			$nextIdentificatorEndPointer,
			sprintf('%s?%s', $identificator, $identificatorDifference)
		);

		$phpcsFile->fixer->endChangeset();

		if ($isConditionOfTernaryOperator) {
			return TokenHelper::findNext($phpcsFile, T_INLINE_THEN, $identicalPointer + 1);
		}

		return $pointerAfterNexIdentificator;
	}

	/**
	 * @return array<int, int>|null
	 */
	private function getNextIdentificator(File $phpcsFile, int $pointerBefore): ?array
	{
		/** @var int $nextIdentificatorStartPointer */
		$nextIdentificatorStartPointer = TokenHelper::findNextEffective($phpcsFile, $pointerBefore + 1);
		$nextIdentificatorEndPointer = $this->findIdentificatorEnd($phpcsFile, $nextIdentificatorStartPointer);

		if ($nextIdentificatorEndPointer === null) {
			return null;
		}

		return [$nextIdentificatorStartPointer, $nextIdentificatorEndPointer];
	}

	private function findIdentificatorStart(File $phpcsFile, int $identificatorEndPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$identificatorEndPointer]['code'] === T_CLOSE_PARENTHESIS) {
			$pointerBeforeParenthesisOpener = TokenHelper::findPreviousEffective(
				$phpcsFile,
				$tokens[$identificatorEndPointer]['parenthesis_opener'] - 1
			);
			$identificatorStartPointer = IdentificatorHelper::findStartPointer($phpcsFile, $pointerBeforeParenthesisOpener);
		} else {
			$identificatorStartPointer = IdentificatorHelper::findStartPointer($phpcsFile, $identificatorEndPointer);
		}

		if ($identificatorStartPointer !== null) {
			$pointerBeforeIdentificatorStart = TokenHelper::findPreviousEffective($phpcsFile, $identificatorStartPointer - 1);

			if (in_array(
				$tokens[$pointerBeforeIdentificatorStart]['code'],
				[T_DOUBLE_COLON, T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR],
				true
			)) {
				$pointerBeforeOperator = TokenHelper::findPreviousEffective($phpcsFile, $pointerBeforeIdentificatorStart - 1);
				return $this->findIdentificatorStart($phpcsFile, $pointerBeforeOperator);
			}
		}

		return $identificatorStartPointer;
	}

	private function findIdentificatorEnd(File $phpcsFile, int $identificatorStartPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		$identificatorEndPointer = $tokens[$identificatorStartPointer]['code'] === T_STRING
			? $identificatorStartPointer
			: IdentificatorHelper::findEndPointer($phpcsFile, $identificatorStartPointer);

		if ($identificatorEndPointer !== null) {
			$pointerAfterIdentificatorEnd = TokenHelper::findNextEffective($phpcsFile, $identificatorEndPointer + 1);

			if ($tokens[$pointerAfterIdentificatorEnd]['code'] === T_OPEN_PARENTHESIS) {
				$identificatorEndPointer = $tokens[$pointerAfterIdentificatorEnd]['parenthesis_closer'];
				$pointerAfterIdentificatorEnd = TokenHelper::findNextEffective($phpcsFile, $identificatorEndPointer + 1);
			}

			if (in_array(
				$tokens[$pointerAfterIdentificatorEnd]['code'],
				[T_DOUBLE_COLON, T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR],
				true
			)) {
				$pointerAfterOperator = TokenHelper::findNextEffective($phpcsFile, $pointerAfterIdentificatorEnd + 1);
				return $this->findIdentificatorEnd($phpcsFile, $pointerAfterOperator);
			}
		}

		return $identificatorEndPointer;
	}

	private function areIdentificatorsCompatible(string $first, string $second): bool
	{
		/** @var list<string> $firstParts */
		$firstParts = preg_split(self::OPERATOR_REGEXP, $first, -1, PREG_SPLIT_DELIM_CAPTURE);
		/** @var list<string> $secondParts */
		$secondParts = preg_split(self::OPERATOR_REGEXP, $second, -1, PREG_SPLIT_DELIM_CAPTURE);

		$minPartsCount = min(count($firstParts), count($secondParts));

		for ($i = 0; $i < $minPartsCount; $i++) {
			if ($firstParts[$i] === '?->' && $secondParts[$i] === '->') {
				continue;
			}

			if ($firstParts[$i] !== $secondParts[$i]) {
				return false;
			}
		}

		return array_key_exists($minPartsCount, $secondParts) && $secondParts[$minPartsCount] === '->';
	}

	private function getIdentificatorDifference(
		File $phpcsFile,
		string $identificator,
		int $nextIdentificatorStartPointer,
		int $nextIdentificatorEndPointer
	): string
	{
		$objectOperatorsCountInIdentificator = substr_count($identificator, '->');

		$tokens = $phpcsFile->getTokens();

		$objectOperatorsCountInNextIdentificator = 0;
		$differencePointer = $nextIdentificatorStartPointer;
		for ($i = $nextIdentificatorStartPointer; $i <= $nextIdentificatorEndPointer; $i++) {
			if (in_array($tokens[$i]['code'], [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR], true)) {
				$objectOperatorsCountInNextIdentificator++;
			}

			if ($objectOperatorsCountInNextIdentificator > $objectOperatorsCountInIdentificator) {
				$differencePointer = $i;
				break;
			}
		}

		return TokenHelper::getContent($phpcsFile, $differencePointer, $nextIdentificatorEndPointer);
	}

	/**
	 * @return array{0: int, 1: int}
	 */
	private function getIdenticalData(File $phpcsFile, int $identicalPointer): array
	{
		/** @var int $pointerBeforeIdentical */
		$pointerBeforeIdentical = TokenHelper::findPreviousEffective($phpcsFile, $identicalPointer - 1);
		/** @var int $pointerAfterIdentical */
		$pointerAfterIdentical = TokenHelper::findNextEffective($phpcsFile, $identicalPointer + 1);

		return [$pointerBeforeIdentical, $pointerAfterIdentical];
	}

	/**
	 * @return array{0: int|null, 1: int|null, 2: int|null}
	 */
	private function getConditionData(File $phpcsFile, int $pointerBeforeIdentical, int $pointerAfterIdentical): array
	{
		$tokens = $phpcsFile->getTokens();

		$isYoda = $tokens[$pointerBeforeIdentical]['code'] === T_NULL;

		if ($isYoda) {
			$identificatorStartPointer = $pointerAfterIdentical;
			$identificatorEndPointer = $this->findIdentificatorEnd($phpcsFile, $identificatorStartPointer);
			$conditionStartPointer = $pointerBeforeIdentical;

		} else {
			$identificatorEndPointer = $pointerBeforeIdentical;
			$identificatorStartPointer = $this->findIdentificatorStart($phpcsFile, $identificatorEndPointer);
			$conditionStartPointer = $identificatorStartPointer;
		}

		return [$identificatorStartPointer, $identificatorEndPointer, $conditionStartPointer];
	}

}
