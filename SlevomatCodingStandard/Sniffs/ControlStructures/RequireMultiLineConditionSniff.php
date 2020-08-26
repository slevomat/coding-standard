<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function in_array;
use function sprintf;
use function strlen;
use const T_CLOSE_PARENTHESIS;
use const T_OPEN_PARENTHESIS;
use const T_WHITESPACE;

class RequireMultiLineConditionSniff extends AbstractLineCondition
{

	public const CODE_REQUIRED_MULTI_LINE_CONDITION = 'RequiredMultiLineCondition';

	/** @var int */
	public $minLineLength = 121;

	/** @var bool */
	public $booleanOperatorOnPreviousLine = false;

	/** @var bool */
	public $alwaysSplitAllConditionParts = false;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $controlStructurePointer
	 */
	public function process(File $phpcsFile, $controlStructurePointer): void
	{
		if ($this->shouldBeSkipped($phpcsFile, $controlStructurePointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$parenthesisOpenerPointer = $tokens[$controlStructurePointer]['parenthesis_opener'];
		$parenthesisCloserPointer = $tokens[$controlStructurePointer]['parenthesis_closer'];

		$booleanOperatorPointers = TokenHelper::findNextAll($phpcsFile, Tokens::$booleanOperators, $parenthesisOpenerPointer + 1, $parenthesisCloserPointer);
		$booleanOperatorPointersCount = count($booleanOperatorPointers);

		if ($booleanOperatorPointersCount === 0) {
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

		if (!$this->shouldReportError($lineLength, $conditionLinesCount, $booleanOperatorPointersCount)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Condition of "%s" should be splitted to more lines so each condition part is on its own line.',
				$this->getControlStructureName($phpcsFile, $controlStructurePointer)
			),
			$controlStructurePointer,
			self::CODE_REQUIRED_MULTI_LINE_CONDITION
		);

		if (!$fix) {
			return;
		}

		$controlStructureIndentation = IndentationHelper::getIndentation(
			$phpcsFile,
			$conditionStartsOnNewLine ? $conditionStartPointer : TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $parenthesisOpenerPointer)
		);

		$conditionIndentation = $conditionStartsOnNewLine
			? $controlStructureIndentation
			: IndentationHelper::addIndentation($controlStructureIndentation);

		$cleanWhitespaceBefore = static function (int $pointer) use ($phpcsFile, $tokens): void {
			for ($j = $pointer - 1; $j > 0; $j--) {
				if ($tokens[$j]['code'] !== T_WHITESPACE) {
					break;
				}

				$phpcsFile->fixer->replaceToken($j, '');
			}
		};

		$cleanWhitespaceAfter = static function (int $pointer) use ($phpcsFile, $tokens): void {
			for ($j = $pointer + 1; $j < count($tokens); $j++) {
				if ($tokens[$j]['code'] !== T_WHITESPACE) {
					break;
				}

				$phpcsFile->fixer->replaceToken($j, '');
			}
		};

		$phpcsFile->fixer->beginChangeset();

		if (!$conditionStartsOnNewLine) {
			$cleanWhitespaceBefore($conditionStartPointer);
			$phpcsFile->fixer->addContentBefore($conditionStartPointer, $phpcsFile->eolChar . $conditionIndentation);
		}

		$innerConditionLevel = 0;

		for ($i = $conditionStartPointer; $i <= $conditionEndPointer; $i++) {
			if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
				$containsBooleanOperator = TokenHelper::findNext($phpcsFile, Tokens::$booleanOperators, $i + 1, $tokens[$i]['parenthesis_closer']) !== null;

				$innerConditionLevel++;

				if ($containsBooleanOperator) {
					$cleanWhitespaceAfter($i);

					$phpcsFile->fixer->addContent(
						$i,
						$phpcsFile->eolChar . IndentationHelper::addIndentation($conditionIndentation, $innerConditionLevel)
					);

					$cleanWhitespaceBefore($tokens[$i]['parenthesis_closer']);

					$phpcsFile->fixer->addContentBefore(
						$tokens[$i]['parenthesis_closer'],
						$phpcsFile->eolChar . IndentationHelper::addIndentation($conditionIndentation, $innerConditionLevel - 1)
					);
				}

				continue;
			}

			if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
				$innerConditionLevel--;
				continue;
			}

			if (!in_array($tokens[$i]['code'], Tokens::$booleanOperators, true)) {
				continue;
			}

			$innerConditionIndentation = $conditionIndentation;
			if ($innerConditionLevel > 0) {
				$innerConditionIndentation = IndentationHelper::addIndentation($innerConditionIndentation, $innerConditionLevel);
			}

			if ($this->booleanOperatorOnPreviousLine) {
				$phpcsFile->fixer->addContent($i, $phpcsFile->eolChar . $innerConditionIndentation);

				$cleanWhitespaceAfter($i);

				continue;

			}

			$cleanWhitespaceBefore($i);

			$phpcsFile->fixer->addContentBefore($i, $phpcsFile->eolChar . $innerConditionIndentation);
		}

		if (!$conditionEndsOnNewLine) {
			$cleanWhitespaceAfter($conditionEndPointer);
			$phpcsFile->fixer->addContent($conditionEndPointer, $phpcsFile->eolChar . $controlStructureIndentation);
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function shouldReportError(int $lineLength, int $conditionLinesCount, int $booleanOperatorPointersCount): bool
	{
		$minLineLength = SniffSettingsHelper::normalizeInteger($this->minLineLength);

		if ($conditionLinesCount === 1) {
			return $minLineLength === 0 || $lineLength >= $minLineLength;
		}

		return $this->alwaysSplitAllConditionParts
			? $conditionLinesCount < $booleanOperatorPointersCount + 1
			: false;
	}

}
