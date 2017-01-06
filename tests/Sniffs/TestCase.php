<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

	/**
	 * @param string $filePath
	 * @param mixed[] $sniffProperties
	 * @param string[] $codesToCheck
	 * @return \PHP_CodeSniffer_File
	 */
	protected function checkFile(string $filePath, array $sniffProperties = [], array $codesToCheck = []): \PHP_CodeSniffer_File
	{
		$codeSniffer = new \PHP_CodeSniffer();
		$codeSniffer->cli->setCommandLineValues([
			'-s',
		]);

		$propertyReflection = new \ReflectionProperty(\PHP_CodeSniffer::class, 'ruleset');
		$propertyReflection->setAccessible(true);

		if (count($sniffProperties) > 0) {
			$ruleset = $propertyReflection->getValue($codeSniffer);
			$ruleset[$this->getSniffName()]['properties'] = $sniffProperties;
			$propertyReflection->setValue($codeSniffer, $ruleset);
		}

		$codeSniffer->registerSniffs([$this->getSniffPath()], [], []);

		if (count($codesToCheck) > 0) {
			$classReflection = new \ReflectionClass($this->getSniffClassName());

			$ruleset = $propertyReflection->getValue($codeSniffer);

			foreach ($classReflection->getConstants() as $constantName => $constantValue) {
				if (strpos($constantName, 'CODE_') === 0 && !in_array($constantValue, $codesToCheck, true)) {
					$ruleset[sprintf('%s.%s', $this->getSniffName(), $constantValue)]['severity'] = 0;
				}
			}

			$propertyReflection->setValue($codeSniffer, $ruleset);
		}

		$codeSniffer->populateTokenListeners();

		return $codeSniffer->processFile($filePath);
	}

	protected function assertNoSniffErrorInFile(\PHP_CodeSniffer_File $file)
	{
		$errors = $file->getErrors();
		$this->assertEmpty($errors, sprintf('No errors expected, but %d errors found.', count($errors)));
	}

	protected function assertSniffError(\PHP_CodeSniffer_File $codeSnifferFile, int $line, string $code, string $message = null)
	{
		$errors = $codeSnifferFile->getErrors();
		$this->assertTrue(isset($errors[$line]), sprintf('Expected error on line %s, but none found.', $line));

		$sniffCode = sprintf('%s.%s', $this->getSniffName(), $code);

		$this->assertTrue(
			$this->hasError($errors[$line], $sniffCode, $message),
			sprintf(
				'Expected error %s%s, but none found on line %d.%sErrors found on line %d:%s%s%s',
				$sniffCode,
				$message !== null ? sprintf(' with message "%s"', $message) : '',
				$line,
				PHP_EOL . PHP_EOL,
				$line,
				PHP_EOL,
				$this->getFormattedErrors($errors[$line]),
				PHP_EOL
			)
		);
	}

	protected function assertNoSniffError(\PHP_CodeSniffer_File $codeSnifferFile, int $line)
	{
		$errors = $codeSnifferFile->getErrors();
		$this->assertFalse(
			isset($errors[$line]),
			sprintf(
				'Expected no error on line %s, but found:%s%s%s',
				$line,
				PHP_EOL . PHP_EOL,
				isset($errors[$line]) ? $this->getFormattedErrors($errors[$line]) : '',
				PHP_EOL
			)
		);
	}

	protected function assertAllFixedInFile(\PHP_CodeSniffer_File $codeSnifferFile)
	{
		$codeSnifferFile->fixer->fixFile();

		$this->assertStringEqualsFile(preg_replace('~(\\.php)$~', '.fixed\\1', $codeSnifferFile->getFilename()), $codeSnifferFile->fixer->getContents());
	}

	private function hasError(array $errorsOnLine, string $sniffCode, string $message = null): bool
	{
		foreach ($errorsOnLine as $errorsOnPosition) {
			foreach ($errorsOnPosition as $error) {
				if (
					$error['source'] === $sniffCode
					&& ($message === null || strpos($error['message'], $message) !== false)
				) {
					return true;
				}
			}
		}

		return false;
	}

	private function getFormattedErrors(array $errors): string
	{
		return implode(PHP_EOL, array_map(function (array $errors): string {
			return implode(PHP_EOL, array_map(function (array $error): string {
				return sprintf("\t%s: %s", $error['source'], $error['message']);
			}, $errors));
		}, $errors));
	}

	protected function getSniffName(): string
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
			$this->getSniffClassName()
		);
	}

	protected function getSniffClassName(): string
	{
		return substr(get_class($this), 0, -strlen('Test'));
	}

	protected function getSniffPath(): string
	{
		$path = $this->getSniffClassName();

		$path = str_replace('\\', '/', $path);
		$path = str_replace('SlevomatCodingStandard', __DIR__ . '/../../SlevomatCodingStandard', $path);

		$path .= '.php';

		return realpath($path);
	}

}
