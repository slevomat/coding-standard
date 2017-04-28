<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

	const UNKNOWN_PHP_TOKEN = 'UNKNOWN';

	/**
	 * @param int|string $code
	 * @param int $line
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param int|null $tokenPointer
	 */
	protected function assertTokenPointer($code, int $line, \PHP_CodeSniffer\Files\File $codeSnifferFile, int $tokenPointer = null)
	{
		$token = $this->getTokenFromPointer($codeSnifferFile, $tokenPointer);
		$expectedTokenName = $this->findTokenName($code);
		$this->assertSame(
			$code,
			$token['code'],
			$expectedTokenName !== null ? sprintf('Expected %s, actual token is %s', $expectedTokenName, $token['type']) : ''
		);
		$this->assertSame($line, $token['line']);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param string $name
	 * @return int|null
	 */
	protected function findClassPointerByName(\PHP_CodeSniffer\Files\File $codeSnifferFile, string $name)
	{
		$tokens = $codeSnifferFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] === T_STRING && $tokens[$i]['content'] === $name) {
				$classPointer = $codeSnifferFile->findPrevious([T_CLASS, T_INTERFACE, T_TRAIT], $i - 1);
				if ($classPointer !== false) {
					return $classPointer;
				}
			}
		}
		return null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param string $name
	 * @return int|null
	 */
	protected function findConstantPointerByName(\PHP_CodeSniffer\Files\File $codeSnifferFile, string $name)
	{
		$tokens = $codeSnifferFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] === T_STRING && $tokens[$i]['content'] === $name) {
				$constantPointer = $codeSnifferFile->findPrevious(T_CONST, $i - 1);
				if ($constantPointer !== false) {
					return $constantPointer;
				}
			}
		}
		return null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param string $name
	 * @return int|null
	 */
	protected function findPropertyPointerByName(\PHP_CodeSniffer\Files\File $codeSnifferFile, string $name)
	{
		$tokens = $codeSnifferFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] === T_VARIABLE && $tokens[$i]['content'] === sprintf('$%s', $name)) {
				$propertyPointer = $codeSnifferFile->findPrevious([T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC], $i - 1);
				if ($propertyPointer !== false) {
					return $i;
				}
			}
		}
		return null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param string $name
	 * @return int|null
	 */
	protected function findFunctionPointerByName(\PHP_CodeSniffer\Files\File $codeSnifferFile, string $name)
	{
		$tokens = $codeSnifferFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] === T_STRING && $tokens[$i]['content'] === $name) {
				$functionPointer = $codeSnifferFile->findPrevious(T_FUNCTION, $i - 1);
				if ($functionPointer !== false) {
					return $functionPointer;
				}
			}
		}
		return null;
	}

	/**
	 * @param int|string $code
	 * @return string|null
	 */
	private function findTokenName($code)
	{
		if (is_int($code)) {
			$tokenName = token_name($code);
			if ($tokenName !== self::UNKNOWN_PHP_TOKEN) {
				return $tokenName;
			}
		}

		// \PHP_CodeSniffer defines more token constants
		$constants = get_defined_constants(true);
		foreach ($constants['user'] as $name => $value) {
			if ($value === $code) {
				return $name;
			}
		}

		return null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param int|null $tokenPointer
	 * @return mixed[]
	 */
	private function getTokenFromPointer(
		\PHP_CodeSniffer\Files\File $codeSnifferFile,
		int $tokenPointer = null
	): array
	{
		if ($tokenPointer === null) {
			throw new \SlevomatCodingStandard\Helpers\NullTokenPointerException();
		}

		$tokens = $codeSnifferFile->getTokens();
		if (!isset($tokens[$tokenPointer])) {
			throw new \SlevomatCodingStandard\Helpers\TokenPointerOutOfBoundsException(
				$tokenPointer,
				TokenHelper::getLastTokenPointer($codeSnifferFile)
			);
		}

		return $tokens[$tokenPointer];
	}

	protected function getCodeSnifferFile(string $filename): \PHP_CodeSniffer\Files\File
	{
		$codeSniffer = new \PHP_CodeSniffer\Runner();
		$codeSniffer->config = new \PHP_CodeSniffer\Config([
			'-s',
		]);
		$codeSniffer->init();

		$codeSnifferFile = new \PHP_CodeSniffer\Files\LocalFile(
			$filename,
			$codeSniffer->ruleset,
			$codeSniffer->config
		);

		$codeSnifferFile->process();

		return $codeSnifferFile;
	}

}
