<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function in_array;
use function sprintf;
use function strlen;
use const T_CLOSE_PARENTHESIS;
use const T_OPEN_PARENTHESIS;

class RequireMultiLineConditionSniff extends AbstractLineCondition
{

	public const CODE_REQUIRED_MULTI_LINE_CONDITION = 'RequiredMultiLineCondition';

	public int $minLineLength = 121;

	public bool $booleanOperatorOnPreviousLine = false;

	public bool $alwaysSplitAllConditionParts = false;

	public function process(File $phpcsFile, int $controlStructurePointer): void
	{
		$this->minLineLength = SniffSettingsHelper::normalizeInteger($this->minLineLength);

		if ($this->shouldBeSkipped($phpcsFile, $controlStructurePointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$parenthesisOpenerPointer = $tokens[$controlStructurePointer]['parenthesis_opener'];
		$parenthesisCloserPointer = $tokens[$controlStructurePointer]['parenthesis_closer'];

		$booleanOperatorPointers = TokenHelper::findNextAll(
			$phpcsFile,
			Tokens::BOOLEAN_OPERATORS,
			$parenthesisOpenerPointer + 1,
			$parenthesisCloserPointer,
		);

		if ($booleanOperatorPointers === []) {
			return;
		}

		$conditionStartPointer = TokenHelper::findNextEffective($phpcsFile, $parenthesisOpenerPointer + 1);
		$conditionEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisCloserPointer - 1);

		$conditionStartsOnNewLine = $tokens[$parenthesisOpenerPointer]['line'] !== $tokens[$conditionStartPointer]['line'];
		$conditionEndsOnNewLine = $tokens[$parenthesisCloserPointer]['line'] !== $tokens[$conditionEndPointer]['line'];

		$lineStart = $this->getLineStart($phpcsFile, $conditionStartsOnNewLine ? $conditionStartPointer - 1 : $parenthesisOpenerPointer);
		$lineEnd = $this->getLineEnd($phpcsFile, $conditionEndsOnNewLine ? $conditionEndPointer + 1 : $parenthesisCloserPointer);

		$condition = $this->getCondition($phpcsFile, $parenthesisOpenerPointer, $parenthesisCloserPointer);

		$lineLength = strlen($lineStart . $condition . $lineEnd);
		$conditionLinesCount = $tokens[$conditionEndPointer]['line'] - $tokens[$conditionStartPointer]['line'] + 1;

		if (!$this->shouldReportError($lineLength, $conditionLinesCount, count($booleanOperatorPointers))) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Condition of "%s" should be split to more lines so each condition part is on its own line.',
				$this->getControlStructureName($phpcsFile, $controlStructurePointer),
			),
			$controlStructurePointer,
			self::CODE_REQUIRED_MULTI_LINE_CONDITION,
		);

		if (!$fix) {
			return;
		}

		$controlStructureIndentation = IndentationHelper::getIndentation(
			$phpcsFile,
			$conditionStartsOnNewLine
				? $conditionStartPointer
				: TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $parenthesisOpenerPointer),
		);

		$conditionIndentation = $conditionStartsOnNewLine
			? $controlStructureIndentation
			: IndentationHelper::addIndentation($phpcsFile, $controlStructureIndentation);

		$innerConditionLevel = 0;

		$phpcsFile->fixer->beginChangeset();

		if (!$conditionStartsOnNewLine) {
			FixerHelper::removeWhitespaceBefore($phpcsFile, $conditionStartPointer);
			FixerHelper::addBefore($phpcsFile, $conditionStartPointer, $phpcsFile->eolChar . $conditionIndentation);
		}

		for ($i = $conditionStartPointer; $i <= $conditionEndPointer; $i++) {
			if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
				$containsBooleanOperator = TokenHelper::findNext(
					$phpcsFile,
					Tokens::BOOLEAN_OPERATORS,
					$i + 1,
					$tokens[$i]['parenthesis_closer'],
				) !== null;

				$innerConditionLevel++;

				if ($containsBooleanOperator) {
					FixerHelper::removeWhitespaceAfter($phpcsFile, $i);

					FixerHelper::add(
						$phpcsFile,
						$i,
						$phpcsFile->eolChar . IndentationHelper::addIndentation($phpcsFile, $conditionIndentation, $innerConditionLevel),
					);

					FixerHelper::removeWhitespaceBefore($phpcsFile, $tokens[$i]['parenthesis_closer']);

					FixerHelper::addBefore(
						$phpcsFile,
						$tokens[$i]['parenthesis_closer'],
						$phpcsFile->eolChar . IndentationHelper::addIndentation(
							$phpcsFile,
							$conditionIndentation,
							$innerConditionLevel - 1,
						),
					);
				}

				continue;
			}

			if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
				$innerConditionLevel--;
				continue;
			}

			if (!in_array($tokens[$i]['code'], Tokens::BOOLEAN_OPERATORS, true)) {
				continue;
			}

			$innerConditionIndentation = $conditionIndentation;
			if ($innerConditionLevel > 0) {
				$innerConditionIndentation = IndentationHelper::addIndentation(
					$phpcsFile,
					$innerConditionIndentation,
					$innerConditionLevel,
				);
			}

			if ($this->booleanOperatorOnPreviousLine) {
				FixerHelper::add($phpcsFile, $i, $phpcsFile->eolChar . $innerConditionIndentation);

				FixerHelper::removeWhitespaceAfter($phpcsFile, $i);

				continue;

			}

			FixerHelper::removeWhitespaceBefore($phpcsFile, $i);

			FixerHelper::addBefore($phpcsFile, $i, $phpcsFile->eolChar . $innerConditionIndentation);
		}

		if (!$conditionEndsOnNewLine) {
			FixerHelper::removeWhitespaceAfter($phpcsFile, $conditionEndPointer);
			FixerHelper::add($phpcsFile, $conditionEndPointer, $phpcsFile->eolChar . $controlStructureIndentation);
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function shouldReportError(int $lineLength, int $conditionLinesCount, int $booleanOperatorPointersCount): bool
	{
		if ($conditionLinesCount === 1) {
			return $this->minLineLength === 0 || $lineLength >= $this->minLineLength;
		}

		return $this->alwaysSplitAllConditionParts
			? $conditionLinesCount < $booleanOperatorPointersCount + 1
			: false;
	}

}
