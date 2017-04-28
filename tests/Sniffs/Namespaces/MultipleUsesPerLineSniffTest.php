<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class MultipleUsesPerLineSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(__DIR__ . '/data/multipleUsesPerLine.php');
	}

	public function testMultipleUsesPerLine()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			5,
			MultipleUsesPerLineSniff::CODE_MULTIPLE_USES_PER_LINE
		);
	}

	public function testIgnoreCommasInClosureUse()
	{
		$this->assertNoSniffError($this->getFileReport(), 7);
	}

}
