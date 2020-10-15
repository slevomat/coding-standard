<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use function count;
use function in_array;
use function sprintf;
use const T_CASE;
use const T_CLOSURE;
use const T_DEFAULT;
use const T_FUNCTION;

final class MaxNestingLevelSniff implements Sniff
{

	public const CODE_MAX_NESTING_LEVEL = 'MaxNestingLevel';
	private const ERROR_MESSAGE = 'Only %d indentation level%s per function/method. Found %s levels.';
	private const SCOPE_CLOSER = 'scope_closer';

	/**	@var int */
	public $maxNestingLevel = 2;

	/**	@var int */
	private $position;

	/**	@var int */
	private $nestingLevel;

	/**	@var int */
	private $currentPtr;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 * @var mixed[]
	 */
	private $ignoredScopeStack = [];

	/**	@var File */
	private $file;

	/**
	 * @return int[]|string[]
	 */
	public function register(): array
	{
		return [T_FUNCTION, T_CLOSURE];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $file
	 * @param int $position
	 */
	public function process(File $file, $position): void
	{
		$this->file = $file;
		$this->position = $position;
		$this->nestingLevel = 0;
		$this->ignoredScopeStack = [];

		$tokens = $file->getTokens();
		$token = $tokens[$position];

		// Ignore abstract methods.
		if (!isset($token['scope_opener'])) {
			return;
		}

		$this->iterateTokens($token['scope_opener'], $token[self::SCOPE_CLOSER], $tokens);

		$this->nestingLevel = $this->subtractFunctionNestingLevel($token);
		$this->handleNestingLevel($this->nestingLevel);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 * @param int $start
	 * @param int $end
	 * @param mixed[] $tokens
	 */
	private function iterateTokens(int $start, int $end, array $tokens): void
	{
		// Find the maximum nesting level of any token in the function.
		for ($this->currentPtr = $start + 1; $this->currentPtr < $end; $this->currentPtr++) {
			$nestedToken = $tokens[$this->currentPtr];

			$this->handleToken($nestedToken);
		}
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 * @param mixed[] $token
	 */
	private function subtractFunctionNestingLevel(array $token): int
	{
		return $this->nestingLevel - $token['level'] - 1;
	}

	private function handleNestingLevel(int $nestingLevel): void
	{
		if ($nestingLevel > $this->maxNestingLevel) {
			$levelPluralization = $this->maxNestingLevel > 1 ? 's' : '';

			$error = sprintf(self::ERROR_MESSAGE, $this->maxNestingLevel, $levelPluralization, $nestingLevel);

			$this->file->addError($error, $this->position, self::CODE_MAX_NESTING_LEVEL);
		}
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 * @param mixed[] $nestedToken
	 */
	private function handleToken(array $nestedToken): void
	{
		$this->handleClosureToken($nestedToken);
		$this->handleCaseToken($nestedToken);

		$this->adjustNestingLevelToIgnoredScope();

		// Calculate nesting level
		$level = $nestedToken['level'] - count($this->ignoredScopeStack);

		if ($this->nestingLevel < $level) {
			$this->nestingLevel = $level;
		}
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 * @param mixed[] $nestedToken
	 */
	private function handleClosureToken(array $nestedToken): void
	{
		if ($nestedToken['code'] === T_CLOSURE) {
			// Move index pointer in case we found a lambda function
			// (another call process will deal with its check later).
			$this->currentPtr = $nestedToken[self::SCOPE_CLOSER];

			return;
		}
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 * @param mixed[] $nestedToken
	 */
	private function handleCaseToken(array $nestedToken): void
	{
		if (in_array($nestedToken['code'], [T_CASE, T_DEFAULT], true)) {
			$this->ignoredScopeStack[] = $nestedToken;

			return;
		}
	}

	private function adjustNestingLevelToIgnoredScope(): void
	{
		// Iterated through ignored scope stack to find out if
		// anything can be popped out and adjust nesting level.
		foreach ($this->ignoredScopeStack as $key => $ignoredScope) {
			$this->unsetScopeIfNotCurrent($key, $ignoredScope);
		}
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
	 * @param int $key
	 * @param mixed[] $ignoredScope
	 */
	private function unsetScopeIfNotCurrent(int $key, array $ignoredScope): void
	{
		if ($ignoredScope[self::SCOPE_CLOSER] !== $this->currentPtr) {
			return;
		}

		unset($this->ignoredScopeStack[$key]);
	}

}
