<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Benchmarks;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Sleep;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use function assert;
use function count;
use function in_array;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strpos;
use function substr;

final class AllSniffBench
{

	/** @return iterable<string, array<string, string>> */
	public function sniffs(): iterable
	{
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(__DIR__ . '/../../SlevomatCodingStandard/Sniffs')
		);

		foreach ($iterator as $file => $info) {
			assert($info instanceof SplFileInfo);
			if (!$info->isReadable()) {
				continue;
			}

			if (strpos($file, 'Sniff.php') === false) {
				continue;
			}

			$path = substr($file, (int) strpos($file, 'SlevomatCodingStandard'));
			$withoutExtension = substr($path, 0, (int) strpos($path, '.php'));
			$fqn = '\\' . str_replace('/', '\\', $withoutExtension);
			$shortName = (new ReflectionClass($fqn))->getShortName();
			$shortName = substr($shortName, 0, (int) strpos($shortName, 'Sniff'));

			yield $shortName => ['sniff' => $fqn];
		}
	}

	/**
	 * phpcs:ignore SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectAnnotationsGroup
	 * @param array<string, string> $sniffConfig
	 * @Sleep(100000)
	 * @OutputTimeUnit("seconds", precision=3)
	 * @ParamProviders({"sniffs"})
	 */
	public function benchSniff(array $sniffConfig): void
	{
		$this->checkFile($sniffConfig['sniff' ], __DIR__ . '/data/BigFile.php');
	}

	/**
	 * @param string $sniffClassName
	 * @param string $filePath
	 * @param array<string, string>  $sniffProperties
	 * @param array<string>  $codesToCheck
	 * @return File
	 */
	private function checkFile(string $sniffClassName, string $filePath, array $sniffProperties = [], array $codesToCheck = []): File
	{
		$sniffName = $this->getSniffName($sniffClassName);
		$codeSniffer = new Runner();
		$codeSniffer->config = new Config(
			[
				'-s',
			]
		);
		$codeSniffer->init();

		if (count($sniffProperties) > 0) {
			$codeSniffer->ruleset->ruleset[$sniffName ]['properties' ] = $sniffProperties;
		}

		$codeSniffer->ruleset->sniffs = [$sniffClassName => new $sniffClassName()];

		if (count($codesToCheck) > 0) {
			foreach ($this->getSniffReflection($sniffClassName)->getConstants() as $constantName => $constantValue) {
				if (strpos($constantName, 'CODE_') !== 0 || in_array($constantValue, $codesToCheck, true)) {
					continue;
				}

				$codeSniffer->ruleset->ruleset[sprintf(
					'%s.%s',
					$sniffName,
					$constantValue
				) ]['severity' ] = 0;
			}
		}

		$codeSniffer->ruleset->populateTokenListeners();

		$file = new LocalFile($filePath, $codeSniffer->ruleset, $codeSniffer->config);
		$file->process();

		return $file;
	}

	private function getSniffName(string $sniffClassName): string
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
			$sniffClassName
		);
	}

	private function getSniffReflection(string $sniffClassName): ReflectionClass
	{
		return new ReflectionClass($sniffClassName);
	}

}
