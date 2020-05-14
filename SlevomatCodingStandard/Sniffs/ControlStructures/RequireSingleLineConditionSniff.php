<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function preg_replace;
use function preg_replace_callback;
use function rtrim;
use function sprintf;
use function str_repeat;
use function strlen;
use function trim;
use const T_ELSEIF;
use const T_IF;
use const T_OPEN_CURLY_BRACKET;
use const T_WHILE;

class RequireSingleLineConditionSniff implements Sniff
{

	public const CODE_REQUIRED_SINGLE_LINE_CONDITION = 'RequiredSingleLineCondition';

	private const IF_CONTROL_STRUCTURE = 'if';
	private const WHILE_CONTROL_STRUCTURE = 'while';
	private const DO_CONTROL_STRUCTURE = 'do';

	/** @var int */
	public $maxLineLength = 120;

	/** @var bool */
	public $alwaysForSimpleConditions = true;

	/** @var string[] */
	public $checkedControlStructures = [
		self::IF_CONTROL_STRUCTURE,
		self::WHILE_CONTROL_STRUCTURE,
		self::DO_CONTROL_STRUCTURE,
	];

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		$this->checkedControlStructures = SniffSettingsHelper::normalizeArray($this->checkedControlStructures);

		$register = [];

		if (in_array(self::IF_CONTROL_STRUCTURE, $this->checkedControlStructures, true)) {
			$register[] = T_IF;
			$register[] = T_ELSEIF;
		}

		if (in_array(self::WHILE_CONTROL_STRUCTURE, $this->checkedControlStructures, true)) {
			$register[] = T_WHILE;
		}

		if (in_array(self::DO_CONTROL_STRUCTURE, $this->checkedControlStructures, true)) {
			$register[] = T_WHILE;
		}

		return $register;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $controlStructurePointer
	 */
	public function process(File $phpcsFile, $controlStructurePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$parenthesisOpenerPointer = $tokens[$controlStructurePointer]['parenthesis_opener'];
		$parenthesisCloserPointer = $tokens[$controlStructurePointer]['parenthesis_closer'];

		if ($tokens[$parenthesisOpenerPointer]['line'] === $tokens[$parenthesisCloserPointer]['line']) {
			return;
		}

		$controlStructureName = $tokens[$controlStructurePointer]['content'];

		if ($tokens[$controlStructurePointer]['code'] === T_WHILE) {
			$pointerAfterParentesisCloser = TokenHelper::findNextEffective($phpcsFile, $parenthesisCloserPointer + 1);
			$isPartOfDo = $tokens[$pointerAfterParentesisCloser]['code'] !== T_OPEN_CURLY_BRACKET;

			if ($isPartOfDo) {
				$controlStructureName = 'do-while';
			}

			if ($isPartOfDo && !in_array(self::DO_CONTROL_STRUCTURE, $this->checkedControlStructures, true)) {
				return;
			}

			if (!$isPartOfDo && !in_array(self::WHILE_CONTROL_STRUCTURE, $this->checkedControlStructures, true)) {
				return;
			}
		}

		$lineStart = $this->getLineStart($phpcsFile, $parenthesisOpenerPointer);
		$condition = $this->getCondition($phpcsFile, $parenthesisOpenerPointer, $parenthesisCloserPointer);
		$lineEnd = $this->getLineEnd($phpcsFile, $parenthesisCloserPointer);

		$lineLength = strlen($lineStart . $condition . $lineEnd);

		$isSimpleCondition = TokenHelper::findNext($phpcsFile, Tokens::$booleanOperators, $parenthesisOpenerPointer + 1, $parenthesisCloserPointer) === null;

		$maxLineLength = SniffSettingsHelper::normalizeInteger($this->maxLineLength);
		if ($maxLineLength !== 0 && $lineLength > $maxLineLength && (!$isSimpleCondition || !$this->alwaysForSimpleConditions)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('Condition of "%s" should be placed on a single line.', $controlStructureName),
			$controlStructurePointer,
			self::CODE_REQUIRED_SINGLE_LINE_CONDITION
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addContent($parenthesisOpenerPointer, $condition);

		for ($i = $parenthesisOpenerPointer + 1; $i < $parenthesisCloserPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function getLineStart(File $phpcsFile, int $parenthesisOpenerPointer): string
	{
		$firstPointerOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $parenthesisOpenerPointer);

		return preg_replace_callback('~^(\t+)~', static function (array $matches): string {
			return str_repeat('    ', strlen($matches[1]));
		}, TokenHelper::getContent($phpcsFile, $firstPointerOnLine, $parenthesisOpenerPointer));
	}

	private function getCondition(File $phpcsFile, int $parenthesisOpenerPointer, int $parenthesisCloserPointer): string
	{
		$condition = TokenHelper::getContent(
			$phpcsFile,
			$parenthesisOpenerPointer + 1,
			$parenthesisCloserPointer - 1
		);

		return trim(preg_replace(sprintf('~%s[ \t]*~', $phpcsFile->eolChar), ' ', $condition));
	}

	private function getLineEnd(File $phpcsFile, int $parenthesisCloserPointer): string
	{
		$firstPointerOnNextLine = TokenHelper::findFirstTokenOnNextLine($phpcsFile, $parenthesisCloserPointer);

		return rtrim(TokenHelper::getContent($phpcsFile, $parenthesisCloserPointer + 1, $firstPointerOnNextLine - 1));
	}

}
