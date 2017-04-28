<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UseDoesNotStartWithBackslashSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(__DIR__ . '/data/useBackslash.php');
	}

	public function testUseStartsWithBackslash()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			3,
			UseDoesNotStartWithBackslashSniff::CODE_STARTS_WITH_BACKSLASH
		);
	}

	public function testDoNotCheckUsedTraitsInClasses()
	{
		$this->assertNoSniffError($this->getFileReport(), 7);
	}

	public function testDoNotCheckUsedTraitsInTraits()
	{
		$this->assertNoSniffError($this->getFileReport(), 13);
	}

}
