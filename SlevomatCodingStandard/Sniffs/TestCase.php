<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs;

use Exception;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;
use function print_r;
use ReflectionClass;
use const PHP_EOL;
use function array_map;
use function count;
use function implode;
use function in_array;
use function preg_replace;
use function sprintf;
use function strlen;
use function strpos;
use function substr;

/**
 * @codeCoverageIgnore
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{

	/**
	 * @param string $filePath
	 * @param mixed[] $sniffProperties
	 * @param string[] $codesToCheck
	 * @return \PHP_CodeSniffer\Files\File
	 */
	protected static function checkFile(string $filePath, array $sniffProperties = [], array $codesToCheck = []): File
	{
		$codeSniffer = new Runner();
		$codeSniffer->config = new Config([
			'-s',
		]);
		$codeSniffer->init();

		if (count($sniffProperties) > 0) {
			$codeSniffer->ruleset->ruleset[static::getSniffName()]['properties'] = $sniffProperties;
		}

		$sniffClassName = static::getSniffClassName();

		$codeSniffer->ruleset->sniffs = [$sniffClassName => new $sniffClassName()];

		if (count($codesToCheck) > 0) {
			foreach (static::getSniffClassReflection()->getConstants() as $constantName => $constantValue) {
				if (strpos($constantName, 'CODE_') !== 0 || in_array($constantValue, $codesToCheck, true)) {
					continue;
				}

				$codeSniffer->ruleset->ruleset[sprintf('%s.%s', static::getSniffName(), $constantValue)]['severity'] = 0;
			}
		}

		$codeSniffer->ruleset->populateTokenListeners();

		$file = new LocalFile($filePath, $codeSniffer->ruleset, $codeSniffer->config);
		$file->process();

		foreach ($file->getErrors() as $errorsOnLine) {
			foreach ($errorsOnLine as $errorsOnPosition) {
				foreach ($errorsOnPosition as $error) {
					if (strpos($error['source'], 'Internal.') === 0) {
						throw new Exception($error['message']);
					}
				}
			}
		}

		return $file;
	}

	protected static function assertNoSniffErrorInFile(File $phpcsFile): void
	{
		$errors = $phpcsFile->getErrors();
		self::assertEmpty($errors, sprintf('No errors expected, but %d errors found. %s', count($errors), print_r
        ($errors, true)));
	}

	protected static function assertSniffError(File $phpcsFile, int $line, string $code, ?string $message = null): void
	{
		$errors = $phpcsFile->getErrors();
		self::assertTrue(isset($errors[$line]), sprintf('Expected error on line %s, but none found.', $line));

		$sniffCode = sprintf('%s.%s', static::getSniffName(), $code);

		self::assertTrue(
			self::hasError($errors[$line], $sniffCode, $message),
			sprintf(
				'Expected error %s%s, but none found on line %d.%sErrors found on line %d:%s%s%s',
				$sniffCode,
				$message !== null ? sprintf(' with message "%s"', $message) : '',
				$line,
				PHP_EOL . PHP_EOL,
				$line,
				PHP_EOL,
				self::getFormattedErrors($errors[$line]),
				PHP_EOL
			)
		);
	}

	protected static function assertNoSniffError(File $phpcsFile, int $line): void
	{
		$errors = $phpcsFile->getErrors();
		self::assertFalse(
			isset($errors[$line]),
			sprintf(
				'Expected no error on line %s, but found:%s%s%s',
				$line,
				PHP_EOL . PHP_EOL,
				isset($errors[$line]) ? self::getFormattedErrors($errors[$line]) : '',
				PHP_EOL
			)
		);
	}

	protected static function assertAllFixedInFile(File $phpcsFile): void
	{
		$phpcsFile->disableCaching();
		$phpcsFile->fixer->fixFile();
		self::assertStringEqualsFile(preg_replace('~(\\.php)$~', '.fixed\\1', $phpcsFile->getFilename()), $phpcsFile->fixer->getContents());
	}

	/**
	 * @param mixed[][][] $errorsOnLine
	 * @param string $sniffCode
	 * @param string|null $message
	 * @return bool
	 */
	private static function hasError(array $errorsOnLine, string $sniffCode, ?string $message = null): bool
	{
		foreach ($errorsOnLine as $errorsOnPosition) {
			foreach ($errorsOnPosition as $error) {
				if (!(
					$error['source'] === $sniffCode
					&& ($message === null || strpos($error['message'], $message) !== false)
				)) {
					continue;
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * @param mixed[][][] $errors
	 */
	private static function getFormattedErrors(array $errors): string
	{
		return implode(PHP_EOL, array_map(function (array $errors): string {
			return implode(PHP_EOL, array_map(function (array $error): string {
				return sprintf("\t%s: %s", $error['source'], $error['message']);
			}, $errors));
		}, $errors));
	}

	protected static function getSniffName(): string
	{
		return preg_replace(
			[
				'~\\\~',
				'~\.Sniffs~',
				'~Sniff$~',
			],
			[
				'.',
				'',
				'',
			],
			static::getSniffClassName()
		);
	}

	protected static function getSniffClassName(): string
	{
		return substr(static::class, 0, -strlen('Test'));
	}

	protected static function getSniffClassReflection(): ReflectionClass
	{
		static $reflections = [];

		$className = static::getSniffClassName();

		return $reflections[$className] ?? $reflections[$className] = new ReflectionClass($className);
	}

}
