<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer;
use PHP_CodeSniffer_File;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

	const UNKNOWN_PHP_TOKEN = 'UNKNOWN';

	/**
	 * @param integer $code
	 * @param integer $line
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer|null $tokenPointer
	 */
	protected function assertTokenPointer($code, $line, $codeSnifferFile, $tokenPointer)
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
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param string $name
	 * @return integer|null
	 */
	protected function findClassPointerByName(\PHP_CodeSniffer_File $codeSnifferFile, $name)
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
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param string $name
	 * @return integer|null
	 */
	protected function findConstantPointerByName(\PHP_CodeSniffer_File $codeSnifferFile, $name)
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
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param string $name
	 * @return integer|null
	 */
	protected function findPropertyPointerByName(\PHP_CodeSniffer_File $codeSnifferFile, $name)
	{
		$tokens = $codeSnifferFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] === T_VARIABLE && $tokens[$i]['content'] === sprintf('$%s', $name)) {
				$propertyPointer = $codeSnifferFile->findPrevious([T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC], $i - 1);
				if ($propertyPointer !== false) {
					return $propertyPointer;
				}
			}
		}
		return null;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param string $name
	 * @return integer|null
	 */
	protected function findFunctionPointerByName(\PHP_CodeSniffer_File $codeSnifferFile, $name)
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
	 * @param integer|string $code
	 * @return string|null
	 */
	private function findTokenName($code)
	{
		if (is_integer($code)) {
			$tokenName = token_name($code);
			if ($tokenName !== self::UNKNOWN_PHP_TOKEN) {
				return $tokenName;
			}
		}

		// PHP_CodeSniffer defines more token constants
		$constants = get_defined_constants(true);
		foreach ($constants['user'] as $name => $value) {
			if ($value === $code) {
				return $name;
			}
		}

		return null;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer|null $tokenPointer
	 * @return mixed[]
	 */
	private function getTokenFromPointer(
		PHP_CodeSniffer_File $codeSnifferFile,
		$tokenPointer
	)
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

	/**
	 * @param string $filename
	 * @return \PHP_CodeSniffer_File
	 */
	protected function getCodeSnifferFile($filename)
	{
		$codeSniffer = new PHP_CodeSniffer();
		$codeSnifferFile = new PHP_CodeSniffer_File(
			$filename,
			[],
			[],
			$codeSniffer
		);
		$codeSnifferFile->start();

		return $codeSnifferFile;
	}

}
