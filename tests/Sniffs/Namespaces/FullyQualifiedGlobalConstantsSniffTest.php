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

	public function testExcludeErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsExcludeErrors.php', [
			'exclude' => ['PHP_VERSION'],
		]);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 28, FullyQualifiedGlobalConstantsSniff::CODE_NON_FULLY_QUALIFIED, 'Constant PHP_OS should be referenced via a fully qualified name.');

		$this->assertAllFixedInFile($report);
	}

	public function testNoErrorsFullyQualifiedDisabled(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsAllowFullyQualifiedDisabledNoErrors.php', ['allowFullyQualified' => false]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrorsFullyQualifiedDisabled(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsAllowFullyQualifiedDisabledErrors.php', ['allowFullyQualified' => false]);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError($report, 13, FullyQualifiedGlobalConstantsSniff::CODE_FULLY_QUALIFIED_NOT_ALLOWED, 'Constant \FOO should not be referenced using the backslash.');
	}

	public function testNoErrorsImportedDisabled(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsAllowImportedDisabledNoErrors.php', ['allowImported' => false]);
		$this->assertNoSniffErrorInFile($report);
	}

	public function testErrorsImportedDisabled(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsAllowImportedDisabledErrors.php', ['allowImported' => false]);

		$this->assertSame(2, $report->getErrorCount());

		$this->assertSniffError($report, 16, FullyQualifiedGlobalConstantsSniff::CODE_IMPORTED_NOT_ALLOWED, 'Constant X should not be referenced using the use statement.');
		$this->assertSniffError($report, 17, FullyQualifiedGlobalConstantsSniff::CODE_IMPORTED_NOT_ALLOWED, 'Constant Z should not be referenced using the use statement.');
	}

}
