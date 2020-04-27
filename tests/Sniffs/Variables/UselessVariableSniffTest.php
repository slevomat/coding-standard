<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Variables;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessVariableSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessVariableNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessVariableErrors.php');

		self::assertSame(19, $report->getErrorCount());

		self::assertSniffError($report, 4, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $a.');
		self::assertSniffError($report, 9, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $b.');
		self::assertSniffError($report, 14, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $c.');
		self::assertSniffError($report, 19, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $d.');
		self::assertSniffError($report, 24, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $e.');
		self::assertSniffError($report, 29, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $f.');
		self::assertSniffError($report, 34, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $g.');
		self::assertSniffError($report, 39, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $h.');
		self::assertSniffError($report, 44, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $i.');
		self::assertSniffError($report, 49, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $j.');
		self::assertSniffError($report, 54, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $k.');
		self::assertSniffError($report, 59, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $l.');
		self::assertSniffError($report, 64, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $m.');
		self::assertSniffError($report, 69, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $n.');
		self::assertSniffError($report, 78, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $o.');
		self::assertSniffError($report, 84, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $p.');
		self::assertSniffError($report, 89, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $q.');
		self::assertSniffError($report, 99, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $r.');
		self::assertSniffError($report, 103, UselessVariableSniff::CODE_USELESS_VARIABLE, 'Useless variable $z.');

		self::assertAllFixedInFile($report);
	}

}
