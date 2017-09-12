<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

/**
 * Bigger value must be on the left side:
 *
 * ($variable, Foo::$class, Foo::bar(), foo())
 *  > (Foo::BAR, BAR)
 *  > (true, false, null, 1, 1.0, arrays, 'foo')
 */
class DisallowYodaComparisonSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_DISALLOWED_YODA_COMPARISON = 'DisallowedYodaComparison';

	const DYNAMISM_VARIABLE = 999;

	const DYNAMISM_CONSTANT = 1;

	const DYNAMISM_FUNCTION_CALL = self::DYNAMISM_VARIABLE;

	/** @var int[] */
	private $tokenDynamism;

	/** @var bool[] */
	private $stopTokenCodes;

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
	 * @return int[]
	 */
	private function getTokenDynamism(): array
	{
		if ($this->tokenDynamism === null) {
			$this->tokenDynamism = [
				T_TRUE => 0,
				T_FALSE => 0,
				T_NULL => 0,
				T_DNUMBER => 0,
				T_LNUMBER => 0,
				T_OPEN_SHORT_ARRAY => 0,
				T_ARRAY => 0, // do not stack error messages when the old-style array syntax is used
				T_CONSTANT_ENCAPSED_STRING => 0,
				T_VARIABLE => self::DYNAMISM_VARIABLE,
				T_STRING => self::DYNAMISM_FUNCTION_CALL,
			];

			$this->tokenDynamism += array_fill_keys(array_keys(\PHP_CodeSniffer\Util\Tokens::$castTokens), 3);
		}

		return $this->tokenDynamism;
	}

	/**
	 * @return bool[]
	 */
	private function getStopTokenCodes(): array
	{
		if ($this->stopTokenCodes === null) {
			$this->stopTokenCodes = [
				T_BOOLEAN_AND => true,
				T_BOOLEAN_OR => true,
				T_SEMICOLON => true,
				T_OPEN_TAG => true,
				T_INLINE_THEN => true,
				T_INLINE_ELSE => true,
				T_LOGICAL_AND => true,
				T_LOGICAL_OR => true,
				T_LOGICAL_XOR => true,
				T_COALESCE => true,
				T_CASE => true,
				T_COLON => true,
				T_RETURN => true,
				T_COMMA => true,
				T_CLOSE_CURLY_BRACKET => true,
			];

			$this->stopTokenCodes += array_fill_keys(array_keys(\PHP_CodeSniffer\Util\Tokens::$assignmentTokens), true);
		}

		return $this->stopTokenCodes;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $comparisonTokenPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $comparisonTokenPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$leftSideTokens = $this->getLeftSideTokens($tokens, $comparisonTokenPointer);
		$rightSideTokens = $this->getRightSideTokens($tokens, $comparisonTokenPointer);
		$leftDynamism = $this->getDynamismForTokens($tokens, $leftSideTokens);
		$rightDynamism = $this->getDynamismForTokens($tokens, $rightSideTokens);

		if ($leftDynamism === null || $rightDynamism === null) {
			return;
		}

		if ($leftDynamism < $rightDynamism) {
			$fix = $phpcsFile->addFixableError('Yoda comparisons are disallowed.', $comparisonTokenPointer, self::CODE_DISALLOWED_YODA_COMPARISON);
			if ($fix && count($leftSideTokens) > 0 & count($rightSideTokens) > 0) {
				$phpcsFile->fixer->beginChangeset();
				$this->write($phpcsFile, $leftSideTokens, $rightSideTokens);
				$this->write($phpcsFile, $rightSideTokens, $leftSideTokens);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed[] $leftSideTokens
	 * @param mixed[] $rightSideTokens
	 */
	private function write(\PHP_CodeSniffer\Files\File $phpcsFile, array $leftSideTokens, array $rightSideTokens)
	{
		current($leftSideTokens);
		$firstLeftPointer = key($leftSideTokens);
		end($leftSideTokens);
		$lastLeftPointer = key($leftSideTokens);

		for ($i = $firstLeftPointer; $i <= $lastLeftPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addContent($firstLeftPointer, implode('', array_map(function (array $token): string {
			return $token['content'];
		}, $rightSideTokens)));
	}

	/**
	 * @param mixed[] $tokens
	 * @param int $comparisonTokenPointer
	 * @return mixed[]
	 */
	private function getLeftSideTokens(array $tokens, int $comparisonTokenPointer): array
	{
		$parenthesisDepth = 0;
		$shortArrayDepth = 0;
		$examinedTokenPointer = $comparisonTokenPointer;
		$sideTokens = [];
		$stopTokenCodes = $this->getStopTokenCodes();
		while (true) {
			$examinedTokenPointer--;
			$examinedToken = $tokens[$examinedTokenPointer];
			if ($parenthesisDepth === 0 && isset($stopTokenCodes[$examinedToken['code']])) {
				break;
			}

			if ($examinedToken['code'] === T_CLOSE_SHORT_ARRAY) {
				$shortArrayDepth++;
			} elseif ($examinedToken['code'] === T_OPEN_SHORT_ARRAY) {
				if ($shortArrayDepth === 0) {
					break;
				}

				$shortArrayDepth--;
			}

			if ($examinedToken['code'] === T_CLOSE_PARENTHESIS) {
				$parenthesisDepth++;
			} elseif ($examinedToken['code'] === T_OPEN_PARENTHESIS) {
				if ($parenthesisDepth === 0) {
					break;
				}

				$parenthesisDepth--;
			}

			$sideTokens[$examinedTokenPointer] = $examinedToken;
		}

		return $this->trimWhitespaceTokens(array_reverse($sideTokens, true));
	}

	/**
	 * @param mixed[] $tokens
	 * @param int $comparisonTokenPointer
	 * @return mixed[]
	 */
	private function getRightSideTokens(array $tokens, int $comparisonTokenPointer): array
	{
		$parenthesisDepth = 0;
		$shortArrayDepth = 0;
		$examinedTokenPointer = $comparisonTokenPointer;
		$sideTokens = [];
		$stopTokenCodes = $this->getStopTokenCodes();
		while (true) {
			$examinedTokenPointer++;
			$examinedToken = $tokens[$examinedTokenPointer];
			if ($parenthesisDepth === 0 && isset($stopTokenCodes[$examinedToken['code']])) {
				break;
			}

			if ($examinedToken['code'] === T_OPEN_SHORT_ARRAY) {
				$shortArrayDepth++;
			} elseif ($examinedToken['code'] === T_CLOSE_SHORT_ARRAY) {
				if ($shortArrayDepth === 0) {
					break;
				}

				$shortArrayDepth--;
			}

			if ($examinedToken['code'] === T_OPEN_PARENTHESIS) {
				$parenthesisDepth++;
			} elseif ($examinedToken['code'] === T_CLOSE_PARENTHESIS) {
				if ($parenthesisDepth === 0) {
					break;
				}

				$parenthesisDepth--;
			}

			$sideTokens[$examinedTokenPointer] = $examinedToken;
		}

		return $this->trimWhitespaceTokens($sideTokens);
	}

	/**
	 * @param mixed[] $tokens
	 * @param mixed[] $sideTokens
	 * @return int|null
	 */
	private function getDynamismForTokens(array $tokens, array $sideTokens)
	{
		$sideTokens = array_values(array_filter($sideTokens, function (array $token): bool {
			return !in_array($token['code'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT, T_NS_SEPARATOR, T_PLUS, T_MINUS, T_INT_CAST, T_DOUBLE_CAST, T_STRING_CAST, T_ARRAY_CAST, T_OBJECT_CAST, T_BOOL_CAST, T_UNSET_CAST], true);
		}));

		$sideTokensCount = count($sideTokens);

		$dynamism = $this->getTokenDynamism();

		if ($sideTokensCount > 0) {
			if ($sideTokens[0]['code'] === T_VARIABLE) {
				// expression starts with a variable - wins over everything else
				return self::DYNAMISM_VARIABLE;
			} elseif ($sideTokens[$sideTokensCount - 1]['code'] === T_CLOSE_PARENTHESIS) {
				if (isset($sideTokens[$sideTokensCount - 1]['parenthesis_owner']) && $tokens[$sideTokens[$sideTokensCount - 1]['parenthesis_owner']]['code'] === T_ARRAY) {
					// array()
					return $dynamism[T_ARRAY];
				} else {
					// function or method call
					return self::DYNAMISM_FUNCTION_CALL;
				}
			} elseif ($sideTokensCount === 1 && $sideTokens[0]['code'] === T_STRING) {
				// constant
				return self::DYNAMISM_CONSTANT;
			}
		}

		if ($sideTokensCount > 2 && $sideTokens[$sideTokensCount - 2]['code'] === T_DOUBLE_COLON) {
			if ($sideTokens[$sideTokensCount - 1]['code'] === T_VARIABLE) {
				// static property access
				return self::DYNAMISM_VARIABLE;
			} elseif ($sideTokens[$sideTokensCount - 1]['code'] === T_STRING) {
				// class constant
				return self::DYNAMISM_CONSTANT;
			}
		}

		if (isset($sideTokens[0]) && isset($dynamism[$sideTokens[0]['code']])) {
			return $dynamism[$sideTokens[0]['code']];
		}

		return null;
	}

	/**
	 * @param mixed[] $tokens
	 * @return mixed[]
	 */
	private function trimWhitespaceTokens(array $tokens): array
	{
		foreach ($tokens as $pointer => $token) {
			if ($token['code'] === T_WHITESPACE) {
				unset($tokens[$pointer]);
			} else {
				break;
			}
		}

		foreach (array_reverse($tokens, true) as $pointer => $token) {
			if ($token['code'] === T_WHITESPACE) {
				unset($tokens[$pointer]);
			} else {
				break;
			}
		}

		return $tokens;
	}

}
