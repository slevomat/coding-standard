<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowGroupUseSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/disallowGroupUseNoErrors.php'));
	}

	public function testErrors(): void
	{
		$codeSnifferFile = self::checkFile(__DIR__ . '/data/disallowGroupUseErrors.php');

		self::assertSniffError($codeSnifferFile, 5, DisallowGroupUseSniff::CODE_DISALLOWED_GROUP_USE);
		self::assertSniffError($codeSnifferFile, 9, DisallowGroupUseSniff::CODE_DISALLOWED_GROUP_USE);
	}

}
