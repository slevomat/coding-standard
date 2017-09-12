<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class DisallowGroupUseSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/disallowGroupUseNoErrors.php'));
	}

	public function testErrors(): void
	{
		$codeSnifferFile = $this->checkFile(__DIR__ . '/data/disallowGroupUseErrors.php');

		$this->assertSniffError($codeSnifferFile, 5, DisallowGroupUseSniff::CODE_DISALLOWED_GROUP_USE);
		$this->assertSniffError($codeSnifferFile, 9, DisallowGroupUseSniff::CODE_DISALLOWED_GROUP_USE);
	}

}
