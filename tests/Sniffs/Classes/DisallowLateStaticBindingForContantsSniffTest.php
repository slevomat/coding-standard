<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowLateStaticBindingForContantsSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowLateStaticBindingForContantsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowLateStaticBindingForContantsErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 13, DisallowLateStaticBindingForContantsSniff::CODE_DISALLOWED_LATE_STATIC_BINDING_FOR_CONSTANT);
		self::assertSniffError($report, 13, DisallowLateStaticBindingForContantsSniff::CODE_DISALLOWED_LATE_STATIC_BINDING_FOR_CONSTANT);

		self::assertAllFixedInFile($report);
	}

}
