<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Sniffs\TestCase;

class MultipleUsesPerLineSniffTest extends TestCase
{

	public function testMultipleUsesPerLine(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			5,
			MultipleUsesPerLineSniff::CODE_MULTIPLE_USES_PER_LINE
		);
	}

	public function testIgnoreCommasInClosureUse(): void
	{
		self::assertNoSniffError($this->getFileReport(), 7);
	}

	private function getFileReport(): File
	{
		return self::checkFile(__DIR__ . '/data/multipleUsesPerLine.php');
	}

}
