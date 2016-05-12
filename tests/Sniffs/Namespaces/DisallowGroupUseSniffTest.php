<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class DisallowGroupUseSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/disallowGroupUseNoErrors.php'));
	}

	public function testErrors()
	{
		$codeSnifferFile = $this->checkFile(__DIR__ . '/data/disallowGroupUseErrors.php');

		$this->assertSniffError($codeSnifferFile, 5);
		$this->assertSniffError($codeSnifferFile, 9);
	}

}
