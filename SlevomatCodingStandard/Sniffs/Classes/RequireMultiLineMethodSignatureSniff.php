<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function sprintf;
use function strlen;
use const T_COMMA;
use const T_WHITESPACE;

class RequireMultiLineMethodSignatureSniff extends AbstractMethodSignature
{

	public const CODE_REQUIRED_MULTI_LINE_SIGNATURE = 'RequiredMultiLineSignature';

	/** @var int */
	public $minLineLength = 121;

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

		if ($tokens[$signatureStartPointer]['line'] < $tokens[$signatureEndPointer]['line']) {
			return;
		}

		$parameters = $phpcsFile->getMethodParameters($methodPointer);
		if (count($parameters) === 0) {
			return;
		}

		$signature = $this->getSignature($phpcsFile, $signatureStartPointer, $signatureEndPointer);
		$signatureWithoutTabIndentation = $this->getSignatureWithoutTabs($signature);

		$minLineLength = SniffSettingsHelper::normalizeInteger($this->minLineLength);
		if ($minLineLength !== 0 && strlen($signatureWithoutTabIndentation) < $minLineLength) {
			return;
		}

		$error = sprintf('Signature of method "%s" should be splitted to more lines so each parameter is on its own line.', FunctionHelper::getName($phpcsFile, $methodPointer));
		$fix = $phpcsFile->addFixableError($error, $methodPointer, self::CODE_REQUIRED_MULTI_LINE_SIGNATURE);
		if (!$fix) {
			return;
		}

		$indentation = $tokens[$signatureStartPointer]['content'];

		$phpcsFile->fixer->beginChangeset();

		foreach ($parameters as $parameter) {
			$pointerBeforeParameter = TokenHelper::findPrevious($phpcsFile, T_COMMA, $parameter['token'] - 1, $tokens[$methodPointer]['parenthesis_opener']);
			if ($pointerBeforeParameter === null) {
				$pointerBeforeParameter = $tokens[$methodPointer]['parenthesis_opener'];
			}

			$phpcsFile->fixer->addContent($pointerBeforeParameter, $phpcsFile->eolChar . IndentationHelper::addIndentation($indentation));
			for ($i = $pointerBeforeParameter + 1; $i < $parameter['token']; $i++) {
				if ($tokens[$i]['code'] !== T_WHITESPACE) {
					break;
				}

				$phpcsFile->fixer->replaceToken($i, '');
			}
		}

		$phpcsFile->fixer->addContentBefore($tokens[$methodPointer]['parenthesis_closer'], $phpcsFile->eolChar . $indentation);

		$phpcsFile->fixer->endChangeset();
	}

}
