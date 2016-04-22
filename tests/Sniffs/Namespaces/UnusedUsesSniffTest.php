<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UnusedUsesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport()
	{
		return $this->checkFile(__DIR__ . '/data/unusedUses.php');
	}

	public function testUnusedUse()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			5,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'First\Object'
		);
	}

	public function testUnusedUseWithAsPart()
	{
		$this->assertSniffError(
			$this->getFileReport(),
			8,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'My\Object (as MyObject)'
		);
	}

	public function testUsedPartialNamespaceUse()
	{
		$this->assertNoSniffError($this->getFileReport(), 6);
	}

	public function testUsedPartialSubnamespaceUse()
	{
		$this->assertNoSniffError($this->getFileReport(), 7);
	}

	public function testUsedUseWithAsPart()
	{
		$this->assertNoSniffError($this->getFileReport(), 9);
	}

	public function testUsedUseInTypehint()
	{
		$this->assertNoSniffError($this->getFileReport(), 10);
	}

	public function testUsedUseWithStaticMethodCall()
	{
		$this->assertNoSniffError($this->getFileReport(), 11);
	}

	public function testFindCaseInsensitiveUse()
	{
		$report = $this->checkFile(__DIR__ . '/data/caseInsensitiveUse.php');
		$this->assertNoSniffError($report, 3);
	}

	public function testReportCaseInsensitiveUse()
	{
		$report = $this->checkFile(__DIR__ . '/data/caseInsensitiveUse.php');
		$this->assertSniffError(
			$report,
			5,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name BAR and use statement Bar do not match'
		);
	}

	public function testUsedTrait()
	{
		$report = $this->checkFile(__DIR__ . '/data/usedTrait.php');
		$this->assertNoSniffError($report, 5);
	}

	public function testTypeWithUnderscoresInAnnotation()
	{
		$report = $this->checkFile(__DIR__ . '/data/unusedUsesAnnotationUnderscores.php', [
			'searchAnnotations' => true,
		]);
		$this->assertNoSniffError($report, 5);
	}

	public function testMatchingCaseOfUseAndClassConstant()
	{
		$report = $this->checkFile(__DIR__ . '/data/matchingCaseOfUseAndClassConstant.php');
		$this->assertNoSniffErrorInFile($report);
	}

}
