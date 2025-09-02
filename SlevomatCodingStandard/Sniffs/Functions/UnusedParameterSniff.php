<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use Exception;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\VariableHelper;
use function array_merge;
use function in_array;
use function preg_match;
use function sprintf;
use function substr;
use const T_COMMA;
use const T_VARIABLE;

class UnusedParameterSniff implements Sniff
{

	public const CODE_UNUSED_PARAMETER = 'UnusedParameter';
	public const CODE_USELESS_SUPPRESS = 'UselessSuppress';

	private const NAME = 'SlevomatCodingStandard.Functions.UnusedParameter';

	/** @var list<string> */
	public array $allowedParameterPatterns = [];

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::FUNCTION_TOKEN_CODES;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $functionPointer
	 */
	public function process(File $phpcsFile, $functionPointer): void
	{
		if (FunctionHelper::isAbstract($phpcsFile, $functionPointer)) {
			return;
		}

		$isSuppressed = SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_UNUSED_PARAMETER));
		$suppressUseless = true;

		$tokens = $phpcsFile->getTokens();

		$currentPointer = $tokens[$functionPointer]['parenthesis_opener'] + 1;
		while (true) {
			$parameterPointer = TokenHelper::findNext(
				$phpcsFile,
				T_VARIABLE,
				$currentPointer,
				$tokens[$functionPointer]['parenthesis_closer'],
			);
			if ($parameterPointer === null) {
				break;
			}

			$previousPointer = TokenHelper::findPrevious(
				$phpcsFile,
				array_merge([T_COMMA], Tokens::$scopeModifiers),
				$parameterPointer - 1,
				$tokens[$functionPointer]['parenthesis_opener'],
			);

			if ($previousPointer !== null && in_array($tokens[$previousPointer]['code'], Tokens::$scopeModifiers, true)) {
				$currentPointer = $parameterPointer + 1;
				continue;
			}

			if (
				$this->variableIsSuppressedViaName($tokens[$parameterPointer]['content']) ||
				VariableHelper::isUsedInScope($phpcsFile, $functionPointer, $parameterPointer)
			) {
				$currentPointer = $parameterPointer + 1;
				continue;
			}

			if (!$isSuppressed) {
				$phpcsFile->addError(
					sprintf('Unused parameter %s.', $tokens[$parameterPointer]['content']),
					$parameterPointer,
					self::CODE_UNUSED_PARAMETER,
				);
			} else {
				$suppressUseless = false;
			}

			$currentPointer = $parameterPointer + 1;
		}

		if (!$isSuppressed || !$suppressUseless) {
			return;
		}

		$phpcsFile->addError(
			sprintf('Useless %s %s', SuppressHelper::ANNOTATION, self::NAME),
			$functionPointer,
			self::CODE_USELESS_SUPPRESS,
		);
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

	private function variableIsSuppressedViaName(string $variableName): bool
	{
		foreach (SniffSettingsHelper::normalizeArray($this->allowedParameterPatterns) as $allowedParamPattern) {
			if (!SniffSettingsHelper::isValidRegularExpression($allowedParamPattern)) {
				throw new Exception(sprintf('%s is not valid PCRE pattern.', $allowedParamPattern));
			}

			if (preg_match($allowedParamPattern, substr($variableName, 1)) === 1) {
				return true;
			}
		}

		return false;
	}

}
