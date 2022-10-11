<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\VariableHelper;
use const T_CLOSURE;
use const T_DOUBLE_QUOTED_STRING;
use const T_FN;
use const T_STATIC;
use const T_VARIABLE;

class ThisInStaticClosureSniff implements Sniff
{

	public const CODE_THIS_IN_STATIC_CLOSURE = 'ThisInStaticClosure';

	/** @return list<int|string> */
	public function register(): array
	{
		return [
			T_CLOSURE,
			T_FN,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $closurePointer
	 */
	public function process(File $phpcsFile, $closurePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $closurePointer - 1);
		if ($tokens[$previousPointer]['code'] !== T_STATIC) {
			return;
		}

		$closureScopeOpenerPointer = $tokens[$closurePointer]['scope_opener'];
		$closureScopeCloserPointer = $tokens[$closurePointer]['scope_closer'];

		$variablePointers = TokenHelper::findNextAll($phpcsFile, T_VARIABLE, $closureScopeOpenerPointer + 1, $closureScopeCloserPointer);
		foreach ($variablePointers as $variablePointer) {
			if ($tokens[$variablePointer]['content'] === '$this') {
				$phpcsFile->addError(
					'"$this" should not be used in a static closure.',
					$variablePointer,
					self::CODE_THIS_IN_STATIC_CLOSURE
				);
			}
		}

		$stringPointers = TokenHelper::findNextAll(
			$phpcsFile,
			T_DOUBLE_QUOTED_STRING,
			$closureScopeOpenerPointer + 1,
			$closureScopeCloserPointer
		);
		foreach ($stringPointers as $stringPointer) {
			if (VariableHelper::isUsedInScopeInString($phpcsFile, '$this', $stringPointer)) {
				$phpcsFile->addError('"$this" should not be used in a static closure.', $stringPointer, self::CODE_THIS_IN_STATIC_CLOSURE);
			}
		}
	}

}
