<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use Exception;
use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use UnexpectedValueException;
use function count;
use function preg_match;
use function sprintf;
use function strlen;
use const T_COMMA;

class RequireMultiLineMethodSignatureSniff extends AbstractMethodSignature
{

	public const CODE_REQUIRED_MULTI_LINE_SIGNATURE = 'RequiredMultiLineSignature';
	private const DEFAULT_MIN_LINE_LENGTH = 121;

	public ?int $minLineLength = null;

	public ?int $minParametersCount = null;

	/** @var list<string> */
	public array $includedMethodPatterns = [];

	/** @var list<string>|null */
	public ?array $includedMethodNormalizedPatterns = null;

	/** @var list<string> */
	public array $excludedMethodPatterns = [];

	/** @var list<string>|null */
	public ?array $excludedMethodNormalizedPatterns = null;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $methodPointer
	 */
	public function process(File $phpcsFile, $methodPointer): void
	{
		$this->minLineLength = SniffSettingsHelper::normalizeNullableInteger($this->minLineLength);
		$this->minParametersCount = SniffSettingsHelper::normalizeNullableInteger($this->minParametersCount);

		if ($this->minLineLength !== null && $this->minParametersCount !== null) {
			throw new UnexpectedValueException('Either minLineLength or minParametersCount can be set.');
		}

		// Maintain backward compatibility if no configuration provided
		if ($this->minLineLength === null && $this->minParametersCount === null) {
			$this->minLineLength = self::DEFAULT_MIN_LINE_LENGTH;
		}

		if (!FunctionHelper::isMethod($phpcsFile, $methodPointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		[$signatureStartPointer, $signatureEndPointer] = $this->getSignatureStartAndEndPointers($phpcsFile, $methodPointer);

		if ($tokens[$signatureStartPointer]['line'] < $tokens[$signatureEndPointer]['line']) {
			return;
		}

		$parameters = $phpcsFile->getMethodParameters($methodPointer);
		$parametersCount = count($parameters);
		if ($parametersCount === 0) {
			return;
		}

		$signature = $this->getSignature($phpcsFile, $signatureStartPointer, $signatureEndPointer);
		$signatureWithoutTabIndentation = $this->getSignatureWithoutTabs($phpcsFile, $signature);
		$methodName = FunctionHelper::getName($phpcsFile, $methodPointer);

		if (
			count($this->includedMethodPatterns) !== 0
			&& !$this->isMethodNameInPatterns($methodName, $this->getIncludedMethodNormalizedPatterns())
		) {
			return;
		}

		if (
			count($this->excludedMethodPatterns) !== 0
			&& $this->isMethodNameInPatterns($methodName, $this->getExcludedMethodNormalizedPatterns())
		) {
			return;
		}

		if ($this->minLineLength !== null && $this->minLineLength !== 0 && strlen($signatureWithoutTabIndentation) < $this->minLineLength) {
			return;
		}

		if ($this->minParametersCount !== null && $parametersCount < $this->minParametersCount) {
			return;
		}

		$error = sprintf('Signature of method "%s" should be split to more lines so each parameter is on its own line.', $methodName);
		$fix = $phpcsFile->addFixableError($error, $methodPointer, self::CODE_REQUIRED_MULTI_LINE_SIGNATURE);
		if (!$fix) {
			return;
		}

		$indentation = $tokens[$signatureStartPointer]['content'];

		$phpcsFile->fixer->beginChangeset();

		foreach ($parameters as $parameter) {
			$pointerBeforeParameter = TokenHelper::findPrevious(
				$phpcsFile,
				T_COMMA,
				$parameter['token'] - 1,
				$tokens[$methodPointer]['parenthesis_opener'],
			);
			if ($pointerBeforeParameter === null) {
				$pointerBeforeParameter = $tokens[$methodPointer]['parenthesis_opener'];
			}

			$phpcsFile->fixer->addContent($pointerBeforeParameter, $phpcsFile->eolChar . IndentationHelper::addIndentation($indentation));

			FixerHelper::removeWhitespaceAfter($phpcsFile, $pointerBeforeParameter);
		}

		$phpcsFile->fixer->addContentBefore($tokens[$methodPointer]['parenthesis_closer'], $phpcsFile->eolChar . $indentation);

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param list<string> $normalizedPatterns
	 */
	private function isMethodNameInPatterns(string $methodName, array $normalizedPatterns): bool
	{
		foreach ($normalizedPatterns as $pattern) {
			if (!SniffSettingsHelper::isValidRegularExpression($pattern)) {
				throw new Exception(sprintf('%s is not valid PCRE pattern.', $pattern));
			}

			if (preg_match($pattern, $methodName) !== 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return list<string>
	 */
	private function getIncludedMethodNormalizedPatterns(): array
	{
		if ($this->includedMethodNormalizedPatterns === null) {
			$this->includedMethodNormalizedPatterns = SniffSettingsHelper::normalizeArray($this->includedMethodPatterns);
		}
		return $this->includedMethodNormalizedPatterns;
	}

	/**
	 * @return list<string>
	 */
	private function getExcludedMethodNormalizedPatterns(): array
	{
		if ($this->excludedMethodNormalizedPatterns === null) {
			$this->excludedMethodNormalizedPatterns = SniffSettingsHelper::normalizeArray($this->excludedMethodPatterns);
		}
		return $this->excludedMethodNormalizedPatterns;
	}

}
