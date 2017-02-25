<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

class TypeNameMatchesFileNameSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testError()
	{
		$report = $this->checkFile(__DIR__ . '/data/rootNamespace/boo.php', [
			'rootNamespaces' => ['tests/Sniffs/Files/data/rootNamespace' => 'RootNamespace'],
		]);

		$this->assertSame(1, $report->getErrorCount());
		$this->assertSniffError($report, 5, TypeNameMatchesFileNameSniff::CODE_NO_MATCH_BETWEEN_TYPE_NAME_AND_FILE_NAME);

		$report = $this->checkFile(__DIR__ . '/data/rootNamespace/coo/Foo.php', [
			'rootNamespaces' => ['tests/Sniffs/Files/data/rootNamespace' => 'RootNamespace'],
		]);

		$this->assertSame(1, $report->getErrorCount());
		$this->assertSniffError($report, 5, TypeNameMatchesFileNameSniff::CODE_NO_MATCH_BETWEEN_TYPE_NAME_AND_FILE_NAME);
	}

	public function testNoError()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/rootNamespace/Foo.php', [
			'rootNamespaces' => ['tests/Sniffs/Files/data/rootNamespace' => 'RootNamespace'],
		]));
	}

	public function testSkippedDir()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/rootNamespace/skippedDir/Bar.php', [
			'rootNamespaces' => ['tests/Sniffs/Files/data/rootNamespace' => 'RootNamespace'],
			'skipDirs' => ['skippedDir'],
		]));
	}

	public function testIgnoredNamespace()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/ignoredNamespace.php', [
			'ignoredNamespaces' => ['IgnoredNamespace'],
		]));
	}

	public function testNoNamespace()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/noNamespace.php'));
	}

}
