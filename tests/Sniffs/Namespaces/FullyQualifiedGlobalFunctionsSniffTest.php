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

	public function testExcludeErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalFunctionsExcludeErrors.php', [
			'exclude' => ['min'],
		]);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 28, FullyQualifiedGlobalFunctionsSniff::CODE_NON_FULLY_QUALIFIED, 'Function max() should be referenced via a fully qualified name.');

		$this->assertAllFixedInFile($report);
	}

	public function testNoErrorsFullyQualifiedDisabled(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalFunctionsAllowFullyQualifiedDisabledNoErrors.php', ['allowFullyQualified' => false]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrorsFullyQualifiedDisabled(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalFunctionsAllowFullyQualifiedDisabledErrors.php', ['allowFullyQualified' => false]);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 10, FullyQualifiedGlobalFunctionsSniff::CODE_FULLY_QUALIFIED_NOT_ALLOWED, 'Function \foo() should not be referenced using the backslash.');
	}

	public function testNoErrorsImportedDisabled(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalFunctionsAllowImportedDisabledNoErrors.php', ['allowImported' => false]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrorsImportedDisabled(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalFunctionsAllowImportedDisabledErrors.php', ['allowImported' => false]);

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 15, FullyQualifiedGlobalFunctionsSniff::CODE_IMPORTED_NOT_ALLOWED, 'Function aa() should not be referenced using the use statement.');
		$this->assertSniffError($report, 16, FullyQualifiedGlobalFunctionsSniff::CODE_IMPORTED_NOT_ALLOWED, 'Function b() should not be referenced using the use statement.');
	}

}
