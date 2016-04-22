<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs;

use PHP_CodeSniffer;
use PHP_CodeSniffer_File;
use ReflectionProperty;

abstract class TestCase extends \Consistence\Sniffs\TestCase
{

	protected function assertNoSniffErrorInFile(PHP_CodeSniffer_File $file)
	{
		$errors = $file->getErrors();
		$this->assertEmpty($errors, sprintf('No errors expected but %d found.', count($errors)));
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param string $filePath
	 * @param mixed[] $sniffProperties
	 * @return \PHP_CodeSniffer_File
	 */
	protected function checkFile($filePath, array $sniffProperties = []): \PHP_CodeSniffer_File
	{
		$codeSniffer = new PHP_CodeSniffer();
		$codeSniffer->cli->setCommandLineValues([
			'-s', // showSources must be on, so that errors are recorded
		]);

		$this->setCodeSnifferRulesetProperties($codeSniffer, $sniffProperties);
		$codeSniffer->registerSniffs([$this->getSniffPath()], [], []);
		$codeSniffer->populateTokenListeners();

		return $codeSniffer->processFile($filePath);
	}

	/**
	 * Uses reflection because PHP_CodeSniffer::setSniffProperties
	 * cannot be used to propagate settings into sniffs' register() method
	 *
	 * @param \PHP_CodeSniffer $codeSniffer
	 * @param mixed[] $sniffProperties
	 */
	private function setCodeSnifferRulesetProperties(PHP_CodeSniffer $codeSniffer, array $sniffProperties)
	{
		if (count($sniffProperties) === 0) {
			return;
		}
		$propertyReflection = new ReflectionProperty(PHP_CodeSniffer::class, 'ruleset');
		$propertyReflection->setAccessible(true);
		$ruleset = $propertyReflection->getValue($codeSniffer);
		$ruleset[$this->getSniffName()]['properties'] = $sniffProperties;
		$propertyReflection->setValue($codeSniffer, $ruleset);
	}

	private function getSniffPath(): string
	{
		// copied from Consistence\Sniffs\TestCase because it's private and I needed to override checkFile
		$path = preg_replace(
			[
				'~\\\~',
				'~SlevomatCodingStandard~',
				'~$~',
			],
			[
				'/',
				__DIR__ . '/../../SlevomatCodingStandard',
				'.php',
			],
			$this->getSniffClassName()
		);

		return realpath($path);
	}

}
