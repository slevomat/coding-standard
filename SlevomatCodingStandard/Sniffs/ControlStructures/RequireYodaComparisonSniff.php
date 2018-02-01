<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\YodaHelper;

/**
 * Bigger value must be on the right side:
 *
 * ($variable, Foo::$class, Foo::bar(), foo())
 *  > (Foo::BAR, BAR)
 *  > (true, false, null, 1, 1.0, arrays, 'foo')
 */
class RequireYodaComparisonSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_REQUIRED_YODA_COMPARISON = 'RequiredYodaComparison';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_IS_IDENTICAL,
			T_IS_NOT_IDENTICAL,
			T_IS_EQUAL,
			T_IS_NOT_EQUAL,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $comparisonTokenPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $comparisonTokenPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$leftSideTokens = YodaHelper::getLeftSideTokens($tokens, $comparisonTokenPointer);
		$rightSideTokens = YodaHelper::getRightSideTokens($tokens, $comparisonTokenPointer);
		$leftDynamism = YodaHelper::getDynamismForTokens($tokens, $leftSideTokens);
		$rightDynamism = YodaHelper::getDynamismForTokens($tokens, $rightSideTokens);

		if ($leftDynamism === null || $rightDynamism === null) {
			return;
		}

		if ($leftDynamism <= $rightDynamism) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Yoda comparison is required.', $comparisonTokenPointer, self::CODE_REQUIRED_YODA_COMPARISON);
		if (!$fix || count($leftSideTokens) === 0 || count($rightSideTokens) === 0) {
			return;
		}

		YodaHelper::fix($phpcsFile, $leftSideTokens, $rightSideTokens);
	}

}
