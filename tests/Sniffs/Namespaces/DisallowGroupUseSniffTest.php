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
		$errors = $this->checkFile(__DIR__ . '/data/disallowGroupUseErrors.php')->getErrors();

		$this->assertSame(2, count($errors));

		$this->assertTrue(isset($errors[5]), sprintf('Expected error on line %s, but none occurred.', 5));
		$this->assertTrue(isset($errors[9]), sprintf('Expected error on line %s, but none occurred.', 9));
	}

}
