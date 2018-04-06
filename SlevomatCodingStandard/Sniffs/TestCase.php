<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs;

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
	protected static function checkFile(string $filePath, array $sniffProperties = [], array $codesToCheck = []): \PHP_CodeSniffer\Files\File
	{
		$codeSniffer = new \PHP_CodeSniffer\Runner();
		$codeSniffer->config = new \PHP_CodeSniffer\Config([
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

		$file = new \PHP_CodeSniffer\Files\LocalFile($filePath, $codeSniffer->ruleset, $codeSniffer->config);
		$file->process();

		foreach ($file->getErrors() as $errorsOnLine) {
			foreach ($errorsOnLine as $errorsOnPosition) {
				foreach ($errorsOnPosition as $error) {
					if (strpos($error['source'], 'Internal.') === 0) {
						throw new \Exception($error['message']);
					}
				}
			}
		}

		return $file;
	}

	protected static function assertNoSniffErrorInFile(\PHP_CodeSniffer\Files\File $file): void
	{
		$errors = $file->getErrors();
		self::assertEmpty($errors, sprintf('No errors expected, but %d errors found.', count($errors)));
	}

	protected static function assertSniffError(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $line, string $code, ?string $message = null): void
	{
		$errors = $codeSnifferFile->getErrors();
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

	protected static function assertNoSniffError(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $line): void
	{
		$errors = $codeSnifferFile->getErrors();
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

	protected static function assertAllFixedInFile(\PHP_CodeSniffer\Files\File $codeSnifferFile): void
	{
		$codeSnifferFile->disableCaching();
		$codeSnifferFile->fixer->fixFile();
		self::assertStringEqualsFile(preg_replace('~(\\.php)$~', '.fixed\\1', $codeSnifferFile->getFilename()), $codeSnifferFile->fixer->getContents());
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

	protected static function getSniffClassReflection(): \ReflectionClass
	{
		static $reflections;

		$className = static::getSniffClassName();

		return $reflections[$className] ?? $reflections[$className] = new \ReflectionClass($className);
	}

}
