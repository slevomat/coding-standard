<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

class NullableTypeForNullDefaultValueSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/nullableTypeForNullDefaultValueNoErrors.php', [
			'enabled' => true,
		]));
	}

	public function testErrors(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/nullableTypeForNullDefaultValueErrors.php', [
			'enabled' => true,
		]);

		$this->assertSame(12, $report->getErrorCount());

		$code = NullableTypeForNullDefaultValueSniff::CODE_NULLABILITY_SYMBOL_REQUIRED;
		$this->assertSniffError($report, 3, $code);
		$this->assertSniffError($report, 8, $code);
		$this->assertSniffError($report, 15, $code);
		$this->assertSniffError($report, 22, $code);
		$this->assertSniffError($report, 27, $code);
		$this->assertSniffError($report, 32, $code);
		$this->assertSniffError($report, 37, $code);
		$this->assertSniffError($report, 42, $code);
		$this->assertSniffError($report, 47, $code);
		$this->assertSniffError($report, 52, $code);
		$this->assertSniffError($report, 57, $code);
		$this->assertSniffError($report, 62, $code);
	}

	public function testFixable(): void
	{
		$codes = [NullableTypeForNullDefaultValueSniff::CODE_NULLABILITY_SYMBOL_REQUIRED];
		$report = $this->checkFile(__DIR__ . '/data/fixableNullableTypeForNullDefaultValue.php', [
			'enabled' => true,
		], $codes);
		$this->assertAllFixedInFile($report);
	}

	public function testDisabledSniff(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/nullableTypeForNullDefaultValueErrors.php', [
			'enabled' => false,
		]);
		$this->assertNoSniffErrorInFile($report);
	}

}
