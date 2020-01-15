<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Sniffs\ControlStructures\AbstractControlStructureSpacingSniff;
use function array_key_exists;
use function array_merge;
use function in_array;
use const T_PARENT;

class ParentCallSpacingSniff extends AbstractControlStructureSpacingSniff
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $parentPointer
	 */
	public function process(File $phpcsFile, $parentPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (array_key_exists('nested_parenthesis', $tokens[$parentPointer])) {
			return;
		}

		$previousEffectivePointer = TokenHelper::findPreviousEffective($phpcsFile, $parentPointer - 1);
		$assignmentAndEqualityTokens = array_merge(Tokens::$assignmentTokens, Tokens::$equalityTokens);
		if (in_array($tokens[$previousEffectivePointer]['code'], $assignmentAndEqualityTokens, true)) {
			return;
		}

		parent::process($phpcsFile, $parentPointer);
	}

	/**
	 * @return string[]
	 */
	protected function getSupportedTokens(): array
	{
		return [T_PARENT];
	}

}
