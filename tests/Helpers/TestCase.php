<?php

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
