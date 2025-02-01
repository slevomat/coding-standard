<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use Exception;
use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use function count;
use function preg_match;
use function sprintf;
use function strlen;

class RequireSingleLineMethodSignatureSniff extends AbstractMethodSignature
{

	public const CODE_REQUIRED_SINGLE_LINE_SIGNATURE = 'RequiredSingleLineSignature';

	public int $maxLineLength = 120;

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
		$this->maxLineLength = SniffSettingsHelper::normalizeInteger($this->maxLineLength);

		if (!FunctionHelper::isMethod($phpcsFile, $methodPointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		[$signatureStartPointer, $signatureEndPointer] = $this->getSignatureStartAndEndPointers($phpcsFile, $methodPointer);

		if ($tokens[$signatureStartPointer]['line'] === $tokens[$signatureEndPointer]['line']) {
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

		if ($this->maxLineLength !== 0 && strlen($signatureWithoutTabIndentation) > $this->maxLineLength) {
			return;
		}

		$error = sprintf('Signature of method "%s" should be placed on a single line.', $methodName);
		$fix = $phpcsFile->addFixableError($error, $methodPointer, self::CODE_REQUIRED_SINGLE_LINE_SIGNATURE);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change($phpcsFile, $signatureStartPointer, $signatureEndPointer, $signature);

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
