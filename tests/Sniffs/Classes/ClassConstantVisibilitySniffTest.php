<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

class ClassConstantVisibilitySniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testErrors()
	{
		$report = $this->checkFile(__DIR__ . '/data/classWithConstants.php');

		$this->assertSame(1, $report->getErrorCount());

		$this->assertNoSniffError($report, 7);
		$this->assertNoSniffError($report, 9);
		$this->assertNoSniffError($report, 10);

		$this->assertSniffError(
			$report,
			6,
			ClassConstantVisibilitySniff::CODE_MISSING_CONSTANT_VISIBILITY,
			'Constant \ClassWithConstants::PUBLIC_FOO visibility missing.'
		);
	}

	public function testNoClassConstants()
	{
		$report = $this->checkFile(__DIR__ . '/data/noClassConstants.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testNoClassConstantsWithNamespace()
	{
		$report = $this->checkFile(__DIR__ . '/data/noClassConstantsWithNamespace.php');
		$this->assertNoSniffErrorInFile($report);
	}

}
