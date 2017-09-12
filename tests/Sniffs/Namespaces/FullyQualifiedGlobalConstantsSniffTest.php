<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedGlobalConstantsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsNoErrors.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsErrors.php');

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 17, FullyQualifiedGlobalConstantsSniff::CODE_NON_FULLY_QUALIFIED, 'Constant PHP_VERSION should be referenced via a fully qualified name.');
		$this->assertSniffError($report, 28, FullyQualifiedGlobalConstantsSniff::CODE_NON_FULLY_QUALIFIED, 'Constant PHP_OS should be referenced via a fully qualified name.');

		$this->assertAllFixedInFile($report);
	}

}
