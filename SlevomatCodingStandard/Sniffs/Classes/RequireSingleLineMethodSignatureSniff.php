<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use function sprintf;
use function strlen;

class RequireSingleLineMethodSignatureSniff extends AbstractMethodSignature
{

	public const CODE_REQUIRED_SINGLE_LINE_SIGNATURE = 'RequiredSingleLineSignature';

	/** @var int */
	public $maxLineLength = 120;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $methodPointer
	 */
	public function process(File $phpcsFile, $methodPointer): void
	{
		if (!FunctionHelper::isMethod($phpcsFile, $methodPointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		[$signatureStartPointer, $signatureEndPointer] = $this->getSignatureStartAndEndPointers($phpcsFile, $methodPointer);

		if ($tokens[$signatureStartPointer]['line'] === $tokens[$signatureEndPointer]['line']) {
			return;
		}

		$signature = $this->getSignature($phpcsFile, $signatureStartPointer, $signatureEndPointer);
		$signatureWithoutTabIndentation = $this->getSignatureWithoutTabs($signature);

		$maxLineLength = SniffSettingsHelper::normalizeInteger($this->maxLineLength);
		if ($maxLineLength !== 0 && strlen($signatureWithoutTabIndentation) > $maxLineLength) {
			return;
		}

		$error = sprintf('Signature of method "%s" should be placed on a single line.', FunctionHelper::getName($phpcsFile, $methodPointer));
		$fix = $phpcsFile->addFixableError($error, $methodPointer, self::CODE_REQUIRED_SINGLE_LINE_SIGNATURE);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->replaceToken($signatureStartPointer, $signature);

		for ($i = $signatureStartPointer + 1; $i <= $signatureEndPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->endChangeset();
	}

}
