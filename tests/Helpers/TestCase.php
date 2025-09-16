<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;
use function count;
use function get_defined_constants;
use function is_int;
use function sprintf;
use function token_name;
use const T_CLASS;
use const T_CONST;
use const T_ENUM;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_STRING;
use const T_TRAIT;
use const T_VARIABLE;

/**
 * @codeCoverageIgnore
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{

	private const UNKNOWN_PHP_TOKEN = 'UNKNOWN';

	protected static function assertPointer(int $expectedPointer, int $actualPointer): void
	{
		self::assertSame(
			$expectedPointer,
			$actualPointer,
			sprintf(
				'Expected pointer %d, but got %d.',
				$expectedPointer,
				$actualPointer,
			),
		);
	}

	/**
	 * @param int|string $code
	 */
	protected function assertTokenPointer($code, int $line, File $phpcsFile, ?int $tokenPointer = null): void
	{
		$token = $this->getTokenFromPointer($phpcsFile, $tokenPointer);
		$expectedTokenName = $this->findTokenName($code);
		self::assertSame(
			$code,
			$token['code'],
			$expectedTokenName !== null ? sprintf('Expected %s, actual token is %s', $expectedTokenName, $token['type']) : '',
		);
		self::assertSame($line, $token['line']);
	}

	protected function findClassPointerByName(File $phpcsFile, string $name): ?int
	{
		$tokens = $phpcsFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] !== T_STRING || $tokens[$i]['content'] !== $name) {
				continue;
			}

			$classPointer = TokenHelper::findPrevious($phpcsFile, [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM], $i - 1);
			if ($classPointer === null) {
				continue;
			}

			return $classPointer;
		}
		return null;
	}

	protected function findConstantPointerByName(File $phpcsFile, string $name): ?int
	{
		$tokens = $phpcsFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] !== T_STRING || $tokens[$i]['content'] !== $name) {
				continue;
			}

			$constantPointer = TokenHelper::findPrevious($phpcsFile, T_CONST, $i - 1);
			if ($constantPointer === null) {
				continue;
			}

			return $constantPointer;
		}
		return null;
	}

	protected function findPropertyPointerByName(File $phpcsFile, string $name): ?int
	{
		$tokens = $phpcsFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] !== T_VARIABLE || $tokens[$i]['content'] !== sprintf('$%s', $name)) {
				continue;
			}

			if (!PropertyHelper::isProperty($phpcsFile, $i)) {
				continue;
			}

			return $i;
		}
		return null;
	}

	protected function findFunctionPointerByName(File $phpcsFile, string $name): ?int
	{
		$tokens = $phpcsFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] !== T_STRING || $tokens[$i]['content'] !== $name) {
				continue;
			}

			$functionPointer = TokenHelper::findPrevious($phpcsFile, T_FUNCTION, $i - 1);
			if ($functionPointer === null) {
				continue;
			}

			return $functionPointer;
		}
		return null;
	}

	/**
	 * @param int|string $tokenCode
	 */
	protected function findPointerByLineAndType(File $phpcsFile, int $line, $tokenCode): ?int
	{
		$tokens = $phpcsFile->getTokens();
		for ($i = 0; $i < count($tokens); $i++) {
			if ($tokens[$i]['line'] > $line) {
				return null;
			}

			if ($tokens[$i]['line'] < $line) {
				continue;
			}

			if ($tokens[$i]['code'] !== $tokenCode) {
				continue;
			}

			return $i;
		}
		return null;
	}

	protected function getCodeSnifferFile(string $filename): File
	{
		$codeSniffer = new Runner();
		$codeSniffer->config = new Config([
			'-s',
		]);
		$codeSniffer->init();

		$codeSniffer->config->tabWidth = 4;
		$phpcsFile = new LocalFile($filename, $codeSniffer->ruleset, $codeSniffer->config);

		$phpcsFile->process();

		return $phpcsFile;
	}

	/**
	 * @param int|string $code
	 */
	private function findTokenName($code): ?string
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
			if ($value !== $code) {
				continue;
			}

			return $name;
		}

		return null;
	}

	/**
	 * @return array{code: int|string, type: string, line: int}
	 */
	private function getTokenFromPointer(File $phpcsFile, ?int $tokenPointer = null): array
	{
		if ($tokenPointer === null) {
			throw new NullTokenPointerException();
		}

		$tokens = $phpcsFile->getTokens();
		if (!isset($tokens[$tokenPointer])) {
			throw new TokenPointerOutOfBoundsException(
				$tokenPointer,
				TokenHelper::getLastTokenPointer($phpcsFile),
			);
		}

		return $tokens[$tokenPointer];
	}

}
