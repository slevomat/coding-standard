<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UnusedUsesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport(): \PHP_CodeSniffer\Files\File
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
		$this->assertSniffError(
			$this->getFileReport(),
			12,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'FooBar\UnusedFunction'
		);
		$this->assertSniffError(
			$this->getFileReport(),
			14,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'FooBar\UNUSED_CONSTANT'
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

	public function testUsedUseInTypeHint()
	{
		$this->assertNoSniffError($this->getFileReport(), 10);
	}

	public function testUsedUseWithStaticMethodCall()
	{
		$this->assertNoSniffError($this->getFileReport(), 11);
	}

	public function testUsedFunction()
	{
		$this->assertNoSniffError($this->getFileReport(), 13);
	}

	public function testUsedConstant()
	{
		$this->assertNoSniffError($this->getFileReport(), 15);
	}

	public function testUsedClassesInImplements()
	{
		$this->assertNoSniffError($this->getFileReport(), 17);
		$this->assertNoSniffError($this->getFileReport(), 18);
	}

	public function testReturnTypeHint()
	{
		$this->assertNoSniffError($this->getFileReport(), 19);
	}

	public function testPartialuses()
	{
		$this->assertNoSniffError($this->getFileReport(), 20);
		$this->assertNoSniffError($this->getFileReport(), 21);
		$this->assertNoSniffError($this->getFileReport(), 22);
	}

	public function testUsedUseInAnnotationWithDisabledSearchAnnotations()
	{
		$report = $this->checkFile(__DIR__ . '/data/unusedUsesInAnnotation.php', [
			'searchAnnotations' => false,
		]);

		$this->assertSame(5, $report->getErrorCount());

		$this->assertSniffError(
			$report,
			5,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type Assert is not used in this file.'
		);
		$this->assertSniffError(
			$report,
			6,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type Doctrine\ORM\Mapping (as ORM) is not used in this file.'
		);
		$this->assertSniffError(
			$report,
			8,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type X is not used in this file.'
		);
		$this->assertSniffError(
			$report,
			9,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type XX is not used in this file.'
		);
	}

	public function testUsedUseInAnnotationWithEnabledSearchAnnotations()
	{
		$report = $this->checkFile(__DIR__ . '/data/unusedUsesInAnnotation.php', [
			'searchAnnotations' => true,
		]);

		$this->assertSame(1, $report->getErrorCount());

		$this->assertSniffError(
			$report,
			9,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type XX is not used in this file.'
		);
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

	public function testMatchingCaseOfUseAndPhpFunction()
	{
		$report = $this->checkFile(__DIR__ . '/data/matchingCaseOfUseAndPhpFunction.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testFixableUnusedUses()
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableUnusedUses.php', [], [UnusedUsesSniff::CODE_UNUSED_USE]);
		$this->assertAllFixedInFile($report);
	}

}
