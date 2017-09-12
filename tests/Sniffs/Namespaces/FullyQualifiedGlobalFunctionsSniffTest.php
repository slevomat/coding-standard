<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedGlobalFunctionsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalFunctionsNoErrors.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalFunctionsErrors.php');

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 17, FullyQualifiedGlobalFunctionsSniff::CODE_NON_FULLY_QUALIFIED, 'Function min() should be referenced via a fully qualified name.');
		$this->assertSniffError($report, 28, FullyQualifiedGlobalFunctionsSniff::CODE_NON_FULLY_QUALIFIED, 'Function max() should be referenced via a fully qualified name.');

		$this->assertAllFixedInFile($report);
	}

}
