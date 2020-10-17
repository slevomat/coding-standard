<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\NamingConventions;

use SlevomatCodingStandard\Sniffs\TestCase;

class ElementNameMinimalLengthSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/elementNameMinimalLengthNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/elementNameMinimalLengthErrors.php');

		self::assertSame(9, $report->getErrorCount());

		self::assertSniffError($report, 5, ElementNameMinimalLengthSniff::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
		self::assertSniffError($report, 12, ElementNameMinimalLengthSniff::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
		self::assertSniffError($report, 17, ElementNameMinimalLengthSniff::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
		self::assertSniffError($report, 21, ElementNameMinimalLengthSniff::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
		self::assertSniffError($report, 25, ElementNameMinimalLengthSniff::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
		self::assertSniffError($report, 31, ElementNameMinimalLengthSniff::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
		self::assertSniffError($report, 40, ElementNameMinimalLengthSniff::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
		self::assertSniffError($report, 48, ElementNameMinimalLengthSniff::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
		self::assertSniffError($report, 56, ElementNameMinimalLengthSniff::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
	}

}
