<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_CLOSURE;
use const T_DOUBLE_COLON;
use const T_DOUBLE_QUOTED_STRING;
use const T_FUNCTION;
use const T_HEREDOC;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_VARIABLE;
use function array_keys;
use function array_reverse;
use function in_array;
use function preg_match;
use function preg_quote;
use function sprintf;
use function substr;

class UnusedParameterSniff implements Sniff
{

	private const NAME = 'SlevomatCodingStandard.Functions.UnusedParameter';

	public const CODE_UNUSED_PARAMETER = 'UnusedParameter';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_CLOSURE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 */
	public function process(File $phpcsFile, $functionPointer): void
	{
		if (FunctionHelper::isAbstract($phpcsFile, $functionPointer)) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_UNUSED_PARAMETER))) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$currentPointer = $tokens[$functionPointer]['parenthesis_opener'] + 1;
		while (true) {
			$parameterPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, $currentPointer, $tokens[$functionPointer]['parenthesis_closer']);
			if ($parameterPointer === null) {
				break;
			}

			$parameterName = $tokens[$parameterPointer]['content'];

			$parameterUsed = false;
			for ($i = $tokens[$functionPointer]['scope_opener'] + 1; $i < $tokens[$functionPointer]['scope_closer']; $i++) {
				$tokenCode = $tokens[$i]['code'];
				$tokenContent = $tokens[$i]['content'];

				if ($tokenCode === T_VARIABLE) {
					if ($this->isParameter($phpcsFile, $i)) {
						continue;
					}
					if ($tokens[$i - 1]['code'] === T_DOUBLE_COLON) {
						continue;
					}
					if ($tokenContent !== $parameterName) {
						continue;
					}
				} elseif (in_array($tokenCode, [T_DOUBLE_QUOTED_STRING, T_HEREDOC], true)) {
					if (!$this->isUsedInString($tokenContent, $parameterName)) {
						continue;
					}
				} elseif ($tokenCode === T_STRING && $tokenContent === 'compact') {
					if (!$this->isUsedInCompactFunction($phpcsFile, $i, $parameterName)) {
						continue;
					}
				} else {
					continue;
				}

				if (!$this->isInSameScope($phpcsFile, $parameterPointer, $i)) {
					continue;
				}

				$parameterUsed = true;
				break;
			}

			if (!$parameterUsed) {
				$phpcsFile->addError(sprintf('Unused parameter %s.', $parameterName), $parameterPointer, self::CODE_UNUSED_PARAMETER);
			}

			$currentPointer = $parameterPointer + 1;
		}
	}

	private function isParameter(File $phpcsFile, int $variablePointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		if (!isset($tokens[$variablePointer]['nested_parenthesis'])) {
			return false;
		}

		$parenthesisOpenerPointer = array_reverse(array_keys($tokens[$variablePointer]['nested_parenthesis']))[0];
		if (!isset($tokens[$parenthesisOpenerPointer]['parenthesis_owner'])) {
			return false;
		}

		$parenthesisOwnerPointer = $tokens[$parenthesisOpenerPointer]['parenthesis_owner'];
		return in_array($tokens[$parenthesisOwnerPointer]['code'], TokenHelper::$functionTokenCodes, true);
	}

	private function isUsedInString(string $stringContent, string $variableName): bool
	{
		return preg_match('~(?<!\\\\)' . preg_quote($variableName, '~') . '\b(?!\()~', $stringContent) > 0;
	}

	private function isUsedInCompactFunction(File $phpcsFile, int $compactFunctionPointer, string $variableName): bool
	{
		$tokens = $phpcsFile->getTokens();

		$variableNameWithoutDollar = substr($variableName, 1);

		$parenthesisOpenerPointer = TokenHelper::findNextEffective($phpcsFile, $compactFunctionPointer + 1);
		if ($tokens[$parenthesisOpenerPointer]['code'] !== T_OPEN_PARENTHESIS) {
			return false;
		}

		for ($i = $parenthesisOpenerPointer + 1; $i < $tokens[$parenthesisOpenerPointer]['parenthesis_closer']; $i++) {
			if (!preg_match('~^([\'"])' . $variableNameWithoutDollar . '\\1$~', $tokens[$i]['content'])) {
				continue;
			}

			return true;
		}

		return false;
	}

	private function isInSameScope(File $phpcsFile, int $parameterPointer, int $pointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$functionScopeLevel = $tokens[$parameterPointer]['level'];
		$isInSameScope = false;
		foreach (array_reverse($tokens[$pointer]['conditions'], true) as $conditionPointer => $conditionTokenCode) {
			if (!in_array($conditionTokenCode, TokenHelper::$functionTokenCodes, true)) {
				continue;
			}

			$isInSameScope = $tokens[$conditionPointer]['level'] === $functionScopeLevel;
			break;
		}

		return $isInSameScope;
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

}
