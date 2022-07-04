<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Complexity;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use function array_filter;
use function array_pop;
use function array_splice;
use function count;
use function in_array;
use const T_BOOLEAN_AND;
use const T_BOOLEAN_OR;
use const T_BREAK;
use const T_CATCH;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_CONTINUE;
use const T_DO;
use const T_ELSE;
use const T_ELSEIF;
use const T_FOR;
use const T_FOREACH;
use const T_FUNCTION;
use const T_GOTO;
use const T_IF;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_OPEN_PARENTHESIS;
use const T_SEMICOLON;
use const T_SWITCH;
use const T_WHILE;

/**
 * Cognitive Complexity
 *
 * @link https://www.sonarsource.com/docs/CognitiveComplexity.pdf
 */
class CognitiveSniff implements Sniff
{

	public const CODE_COMPLEXITY = 'ComplexityTooHigh';

	/**
	 * B1. Increments
	 *
	 * Boolean operators are handled separately due to their chain logic.
	 */
	private const INCREMENTS = [
		T_CATCH => T_CATCH,
		T_DO => T_DO,
		T_ELSE => T_ELSE,
		T_ELSEIF => T_ELSEIF,
		T_FOR => T_FOR,
		T_FOREACH => T_FOREACH,
		T_IF => T_IF,
		T_SWITCH => T_SWITCH,
		T_WHILE => T_WHILE,
	];

	private const BOOLEAN_OPERATORS = [
		T_BOOLEAN_AND => T_BOOLEAN_AND,
		T_BOOLEAN_OR => T_BOOLEAN_OR,
	];

	private const OPERATOR_CHAIN_BREAKS = [
		T_OPEN_PARENTHESIS => T_OPEN_PARENTHESIS,
		T_CLOSE_PARENTHESIS => T_CLOSE_PARENTHESIS,
		T_SEMICOLON => T_SEMICOLON,
		T_INLINE_THEN => T_INLINE_THEN,
		T_INLINE_ELSE => T_INLINE_ELSE,
	];

	/**
	 * B3. Nesting increments
	 */
	private const NESTING_INCREMENTS = [
		T_CLOSURE => T_CLOSURE,
		// increments, but does not receive
		T_ELSEIF => T_ELSEIF,
		// increments, but does not receive
		T_ELSE => T_ELSE,
		T_IF => T_IF,
		T_INLINE_THEN => T_INLINE_THEN,
		T_SWITCH => T_SWITCH,
		T_FOR => T_FOR,
		T_FOREACH => T_FOREACH,
		T_WHILE => T_WHILE,
		T_DO => T_DO,
		T_CATCH => T_CATCH,
	];

	/**
	 * B1. Increments
	 */
	private const BREAKING_TOKENS = [
		T_CONTINUE => T_CONTINUE,
		T_GOTO => T_GOTO,
		T_BREAK => T_BREAK,
	];

	/** @var int */
	public $maxComplexity = 5;

	/** @var int */
	private $cognitiveComplexity = 0;

	/** @var int|string */
	private $lastBooleanOperator = 0;

	/** @var File */
	private $phpcsFile;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CLOSURE,
			T_FUNCTION,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stackPtr
	 */
	public function process(File $phpcsFile, $stackPtr): void
	{
		$this->phpcsFile = $phpcsFile;

		if ($phpcsFile->getCondition($stackPtr, T_FUNCTION) !== false) {
			return;
		}

		$cognitiveComplexity = $this->computeForFunctionFromTokensAndPosition($stackPtr);

		if ($cognitiveComplexity <= $this->maxComplexity) {
			return;
		}

		$name = $phpcsFile->getDeclarationName($stackPtr);

		$phpcsFile->addError(
			'Cognitive complexity for "%s" is %d but has to be less than or equal to %d.',
			$stackPtr,
			self::CODE_COMPLEXITY,
			[
				$name,
				$cognitiveComplexity,
				$this->maxComplexity,
			]
		);
	}

	public function computeForFunctionFromTokensAndPosition(int $position): int
	{
		if (FunctionHelper::isAbstract($this->phpcsFile, $position)) {
			return 0;
		}

		$tokens = $this->phpcsFile->getTokens();

		// Detect start and end of this function definition
		$functionStartPosition = $tokens[$position]['scope_opener'];
		$functionEndPosition = $tokens[$position]['scope_closer'];

		$this->lastBooleanOperator = 0;
		$this->cognitiveComplexity = 0;

		/*
			Keep track of parser's level stack
			We push to this stak whenever we encounter a Tokens::$scopeOpeners
		*/
		$levelStack = [];
		/*
			We look for changes in token[level] to know when to remove from the stack
			however ['level'] only increases when there are tokens inside {}
			after pushing to the stack watch for a level change
		*/
		$levelIncreased = false;

		for ($i = $functionStartPosition + 1; $i < $functionEndPosition; $i++) {
			$currentToken = $tokens[$i];

			$isNestingToken = false;
			if (in_array($currentToken['code'], Tokens::$scopeOpeners, true)) {
				$isNestingToken = true;
				if ($levelIncreased === false && count($levelStack) > 0) {
					// parser's level never increased
					// caused by empty condition such as `if ($x) { }`
					array_pop($levelStack);
				}
				$levelStack[] = $currentToken;
				$levelIncreased = false;
			} elseif (isset($tokens[$i - 1]) && $currentToken['level'] < $tokens[$i - 1]['level']) {
				$diff = $tokens[$i - 1]['level'] - $currentToken['level'];
				array_splice($levelStack, 0 - $diff);
			} elseif (isset($tokens[$i - 1]) && $currentToken['level'] > $tokens[$i - 1]['level']) {
				$levelIncreased = true;
			}

			$this->resolveBooleanOperatorChain($currentToken);

			if (!$this->isIncrementingToken($currentToken, $tokens, $i)) {
				continue;
			}

			$this->cognitiveComplexity++;

			$addNestingIncrement = isset(self::NESTING_INCREMENTS[$currentToken['code']])
				&& in_array($currentToken['code'], [T_ELSEIF, T_ELSE], true) === false;
			if (!$addNestingIncrement) {
				continue;
			}
			$measuredNestingLevel = count(array_filter($levelStack, static function (array $token) {
				return in_array($token['code'], self::NESTING_INCREMENTS, true);
			}));
			if ($isNestingToken) {
				$measuredNestingLevel--;
			}
			// B3. Nesting increment
			if ($measuredNestingLevel > 0) {
				$this->cognitiveComplexity += $measuredNestingLevel;
			}
		}

		return $this->cognitiveComplexity;
	}

	/**
	 * Keep track of consecutive matching boolean operators, that don't receive increment.
	 *
	 * @param array{code:int|string} $token
	 */
	private function resolveBooleanOperatorChain(array $token): void
	{
		$code = $token['code'];

		// Whenever we cross anything that interrupts possible condition we reset chain.
		if ($this->lastBooleanOperator > 0 && isset(self::OPERATOR_CHAIN_BREAKS[$code])) {
			$this->lastBooleanOperator = 0;
			return;
		}

		if (isset(self::BOOLEAN_OPERATORS[$code]) === false) {
			return;
		}

		// If we match last operator, there is no increment added for current one.
		if ($this->lastBooleanOperator === $code) {
			return;
		}

		$this->cognitiveComplexity++;

		$this->lastBooleanOperator = $code;
	}

	/**
	 * @param array{code:int|string} $token
	 * @param array<int, array<string, array<int, int|string>|int|string>> $tokens
	 */
	private function isIncrementingToken(array $token, array $tokens, int $position): bool
	{
		$code = $token['code'];

		if (isset(self::INCREMENTS[$code])) {
			return true;
		}

		// B1. ternary operator
		if ($code === T_INLINE_THEN) {
			return true;
		}

		// B1. goto LABEL, break LABEL, continue LABEL
		if (isset(self::BREAKING_TOKENS[$code])) {
			$nextToken = $this->phpcsFile->findNext(Tokens::$emptyTokens, $position + 1, null, true);
			if ($nextToken === false || $tokens[$nextToken]['code'] !== T_SEMICOLON) {
				return true;
			}
		}

		return false;
	}

}
