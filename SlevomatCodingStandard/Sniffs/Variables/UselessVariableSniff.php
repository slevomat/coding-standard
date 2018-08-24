<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Variables;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_AND_EQUAL;
use const T_CLOSE_CURLY_BRACKET;
use const T_CONCAT_EQUAL;
use const T_DIV_EQUAL;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_EQUAL;
use const T_MINUS_EQUAL;
use const T_MOD_EQUAL;
use const T_MUL_EQUAL;
use const T_OR_EQUAL;
use const T_PLUS_EQUAL;
use const T_POW_EQUAL;
use const T_RETURN;
use const T_SEMICOLON;
use const T_SL_EQUAL;
use const T_SR_EQUAL;
use const T_STATIC;
use const T_VARIABLE;
use const T_WHITESPACE;
use const T_XOR_EQUAL;
use function array_key_exists;
use function array_reverse;
use function count;
use function in_array;
use function preg_match;
use function preg_quote;
use function sprintf;

class UselessVariableSniff implements Sniff
{

	public const CODE_USELESS_VARIABLE = 'UselessVariable';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_RETURN,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $returnPointer
	 */
	public function process(File $phpcsFile, $returnPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $variablePointer */
		$variablePointer = TokenHelper::findNextEffective($phpcsFile, $returnPointer + 1);
		if ($tokens[$variablePointer]['code'] !== T_VARIABLE) {
			return;
		}

		$returnSemicolonPointer = TokenHelper::findNextEffective($phpcsFile, $variablePointer + 1);
		if ($tokens[$returnSemicolonPointer]['code'] !== T_SEMICOLON) {
			return;
		}

		$variableName = $tokens[$variablePointer]['content'];

		$previousVariablePointer = $this->findPreviousVariablePointer($phpcsFile, $returnPointer, $variableName);
		if ($previousVariablePointer === null) {
			return;
		}

		if (!$this->isAssigmentToVariable($phpcsFile, $previousVariablePointer)) {
			return;
		}

		if ($this->isStaticVariable($phpcsFile, $previousVariablePointer)) {
			return;
		}

		if ($this->hasVariableVarAnnotation($phpcsFile, $previousVariablePointer)) {
			return;
		}

		if ($this->hasAnotherAssigmentBefore($phpcsFile, $previousVariablePointer, $variableName)) {
			return;
		}

		for ($i = $variablePointer + 1; $i < count($tokens); $i++) {
			if (
				in_array($tokens[$i]['code'], TokenHelper::$functionTokenCodes, true)
				&& $this->isInSameScope($phpcsFile, $variablePointer, $i)
			) {
				$i = $tokens[$i]['scope_closer'];
				continue;
			}

			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			if ($tokens[$i]['content'] !== $variableName) {
				continue;
			}

			if ($this->isInSameScope($phpcsFile, $variablePointer, $i)) {
				return;
			}
		}

		$errorParameters = [
			sprintf('Useless variable %s.', $variableName),
			$previousVariablePointer,
			self::CODE_USELESS_VARIABLE,
		];

		$pointerAfterReturnSemicolon = TokenHelper::findNextEffective($phpcsFile, $returnSemicolonPointer + 1);

		if (
			$tokens[$pointerAfterReturnSemicolon]['code'] !== T_CLOSE_CURLY_BRACKET
			|| !array_key_exists('scope_condition', $tokens[$pointerAfterReturnSemicolon])
			|| !in_array($tokens[$tokens[$pointerAfterReturnSemicolon]['scope_condition']]['code'], TokenHelper::$functionTokenCodes, true)
		) {
			$phpcsFile->addError(...$errorParameters);
			return;
		}

		$previousVariableSemicolonPointer = null;
		for ($i = $previousVariablePointer + 1; $i < $returnPointer - 1; $i++) {
			if ($tokens[$i]['code'] !== T_SEMICOLON) {
				continue;
			}

			if (!$this->isInSameScope($phpcsFile, $previousVariablePointer, $i)) {
				continue;
			}

			$previousVariableSemicolonPointer = $i;
			break;
		}
		$pointerAfterPreviousVariableSemicolon = TokenHelper::findNextEffective($phpcsFile, $previousVariableSemicolonPointer + 1);

		if ($returnPointer !== $pointerAfterPreviousVariableSemicolon) {
			$phpcsFile->addError(...$errorParameters);
			return;
		}

		$fix = $phpcsFile->addFixableError(...$errorParameters);

		if (!$fix) {
			return;
		}

		/** @var int $assigmentPointer */
		$assigmentPointer = TokenHelper::findNextEffective($phpcsFile, $previousVariablePointer + 1);

		$assigmentFixerMapping = [
			T_PLUS_EQUAL => '+',
			T_MINUS_EQUAL => '-',
			T_MUL_EQUAL => '*',
			T_DIV_EQUAL => '/',
			T_POW_EQUAL => '**',
			T_MOD_EQUAL => '%',
			T_AND_EQUAL => '&',
			T_OR_EQUAL => '|',
			T_XOR_EQUAL => '^',
			T_SL_EQUAL => '<<',
			T_SR_EQUAL => '>>',
			T_CONCAT_EQUAL => '.',
		];

		$phpcsFile->fixer->beginChangeset();

		if ($tokens[$assigmentPointer]['code'] === T_EQUAL) {
			for ($i = $previousVariablePointer; $i < $assigmentPointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
			$phpcsFile->fixer->replaceToken($assigmentPointer, 'return');
		} else {
			$phpcsFile->fixer->addContentBefore($previousVariablePointer, 'return ');
			$phpcsFile->fixer->replaceToken($assigmentPointer, $assigmentFixerMapping[$tokens[$assigmentPointer]['code']]);
		}

		for ($i = $previousVariableSemicolonPointer + 1; $i <= $returnSemicolonPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function isInSameScope(File $phpcsFile, int $firstPointer, int $secondPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$getScopeLevel = function (int $pointer) use ($tokens): int {
			foreach (array_reverse($tokens[$pointer]['conditions'], true) as $conditionPointer => $conditionTokenCode) {
				if (!in_array($conditionTokenCode, TokenHelper::$functionTokenCodes, true)) {
					continue;
				}

				return $tokens[$conditionPointer]['level'];
			}

			return $tokens[$pointer]['level'];
		};

		return $getScopeLevel($firstPointer) === $getScopeLevel($secondPointer);
	}

	private function findPreviousVariablePointer(File $phpcsFile, int $pointer, string $variableName): ?int
	{
		$tokens = $phpcsFile->getTokens();

		for ($i = $pointer - 1; $i >= 0; $i--) {
			if (
				in_array($tokens[$i]['code'], TokenHelper::$functionTokenCodes, true)
				&& $this->isInSameScope($phpcsFile, $tokens[$i]['scope_opener'] + 1, $pointer)
			) {
				return null;
			}

			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			if ($tokens[$i]['content'] !== $variableName) {
				continue;
			}

			if (!$this->isInSameScope($phpcsFile, $i, $pointer)) {
				continue;
			}

			return $i;
		}

		return null;
	}

	private function isAssigmentToVariable(File $phpcsFile, int $pointer): bool
	{
		$assigmentPointer = TokenHelper::findNextEffective($phpcsFile, $pointer + 1);
		return in_array($phpcsFile->getTokens()[$assigmentPointer]['code'], [
			T_EQUAL,
			T_PLUS_EQUAL,
			T_MINUS_EQUAL,
			T_MUL_EQUAL,
			T_DIV_EQUAL,
			T_POW_EQUAL,
			T_MOD_EQUAL,
			T_AND_EQUAL,
			T_OR_EQUAL,
			T_XOR_EQUAL,
			T_SL_EQUAL,
			T_SR_EQUAL,
			T_CONCAT_EQUAL,
		], true);
	}

	private function isStaticVariable(File $phpcsFile, int $variablePointer): bool
	{
		$pointerBeforeVariable = TokenHelper::findPreviousEffective($phpcsFile, $variablePointer - 1);
		return $phpcsFile->getTokens()[$pointerBeforeVariable]['code'] === T_STATIC;
	}

	private function hasVariableVarAnnotation(File $phpcsFile, int $variablePointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$pointerBeforeVariable = TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $variablePointer - 1);
		if ($tokens[$pointerBeforeVariable]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
			return false;
		}

		$docCommentContent = TokenHelper::getContent($phpcsFile, $tokens[$pointerBeforeVariable]['comment_opener'], $pointerBeforeVariable);
		return preg_match('~@var\\s+\\S+\\s+' . preg_quote($tokens[$variablePointer]['content'], '~') . '~', $docCommentContent) > 0;
	}

	private function hasAnotherAssigmentBefore(File $phpcsFile, int $variablePointer, string $variableName): bool
	{
		$previousVariablePointer = $this->findPreviousVariablePointer($phpcsFile, $variablePointer, $variableName);
		if ($previousVariablePointer === null) {
			return false;
		}

		if (!$this->isAssigmentToVariable($phpcsFile, $previousVariablePointer)) {
			return false;
		}

		$previousVariableSemicolonPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $previousVariablePointer + 1);
		$pointerAfterPreviousVariableSemicolon = TokenHelper::findNextEffective($phpcsFile, $previousVariableSemicolonPointer + 1);

		return $pointerAfterPreviousVariableSemicolon === $variablePointer;
	}

}
