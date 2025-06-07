<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use function strlen;

class RequireSingleLineConditionSniff extends AbstractLineCondition
{

	public const CODE_REQUIRED_SINGLE_LINE_CONDITION = 'RequiredSingleLineCondition';

	public int $maxLineLength = 120;

	public bool $alwaysForSimpleConditions = true;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $controlStructurePointer
	 */
	public function process(File $phpcsFile, $controlStructurePointer): void
	{
		$this->maxLineLength = SniffSettingsHelper::normalizeInteger($this->maxLineLength);

		if ($this->shouldBeSkipped($phpcsFile, $controlStructurePointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$parenthesisOpenerPointer = $tokens[$controlStructurePointer]['parenthesis_opener'];
		$parenthesisCloserPointer = $tokens[$controlStructurePointer]['parenthesis_closer'];

		if ($tokens[$parenthesisOpenerPointer]['line'] === $tokens[$parenthesisCloserPointer]['line']) {
			return;
		}

		if (TokenHelper::findNext(
			$phpcsFile,
			TokenHelper::INLINE_COMMENT_TOKEN_CODES,
			$parenthesisOpenerPointer + 1,
			$parenthesisCloserPointer,
		) !== null) {
			return;
		}

		$lineStart = $this->getLineStart($phpcsFile, $parenthesisOpenerPointer);
		$condition = $this->getCondition($phpcsFile, $parenthesisOpenerPointer, $parenthesisCloserPointer);
		$lineEnd = $this->getLineEnd($phpcsFile, $parenthesisCloserPointer);

		$lineLength = strlen($lineStart . $condition . $lineEnd);
		$isSimpleCondition = TokenHelper::findNext(
			$phpcsFile,
			Tokens::$booleanOperators,
			$parenthesisOpenerPointer + 1,
			$parenthesisCloserPointer,
		) === null;

		if (!$this->shouldReportError($lineLength, $isSimpleCondition)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Condition of "%s" should be placed on a single line.',
				$this->getControlStructureName($phpcsFile, $controlStructurePointer),
			),
			$controlStructurePointer,
			self::CODE_REQUIRED_SINGLE_LINE_CONDITION,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addContent($parenthesisOpenerPointer, $condition);

		FixerHelper::removeBetween($phpcsFile, $parenthesisOpenerPointer, $parenthesisCloserPointer);

		$phpcsFile->fixer->endChangeset();
	}

	private function shouldReportError(int $lineLength, bool $isSimpleCondition): bool
	{
		if ($this->maxLineLength === 0) {
			return true;
		}

		if ($lineLength <= $this->maxLineLength) {
			return true;
		}

		return $isSimpleCondition && $this->alwaysForSimpleConditions;
	}

}
