<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UnusedUsesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return $this->checkFile(__DIR__ . '/data/unusedUses.php');
	}

	public function testUnusedUse(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			5,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'First\ObjectPrototype'
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

	public function testUnusedUseWithAsPart(): void
	{
		$this->assertSniffError(
			$this->getFileReport(),
			8,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'My\ObjectPrototype (as MyObject)'
		);
	}

	public function testUsedPartialNamespaceUse(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 6);
	}

	public function testUsedPartialSubnamespaceUse(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 7);
	}

	public function testUsedUseWithAsPart(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 9);
	}

	public function testUsedUseInTypeHint(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 10);
	}

	public function testUsedUseWithStaticMethodCall(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 11);
	}

	public function testUsedFunction(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 13);
	}

	public function testUsedConstant(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 15);
	}

	public function testUsedClassesInImplements(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 17);
		$this->assertNoSniffError($this->getFileReport(), 18);
	}

	public function testReturnTypeHint(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 19);
	}

	public function testPartialUses(): void
	{
		$this->assertNoSniffError($this->getFileReport(), 20);
		$this->assertNoSniffError($this->getFileReport(), 21);
		$this->assertNoSniffError($this->getFileReport(), 22);
		$this->assertNoSniffError($this->getFileReport(), 23);
	}

	public function testUsedUseInAnnotationWithDisabledSearchAnnotations(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/unusedUsesInAnnotation.php', [
			'searchAnnotations' => false,
		]);

		$this->assertSame(6, $report->getErrorCount());

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
		$this->assertSniffError(
			$report,
			10,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type XXX is not used in this file.'
		);
	}

	public function testUsedUseInAnnotationWithEnabledSearchAnnotations(): void
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

	public function testFindCaseInsensitiveUse(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/caseInsensitiveUse.php');
		$this->assertNoSniffError($report, 3);
	}

	public function testReportCaseInsensitiveUse(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/caseInsensitiveUse.php');
		$this->assertSniffError(
			$report,
			5,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name BAR and use statement Bar do not match'
		);
	}

	public function testUsedTrait(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/usedTrait.php');
		$this->assertNoSniffError($report, 5);
	}

	public function testTypeWithUnderscoresInAnnotation(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/unusedUsesAnnotationUnderscores.php', [
			'searchAnnotations' => true,
		]);
		$this->assertNoSniffError($report, 5);
	}

	public function testMatchingCaseOfUseAndClassConstant(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/matchingCaseOfUseAndClassConstant.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testMatchingCaseOfUseAndPhpFunction(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/matchingCaseOfUseAndPhpFunction.php');
		$this->assertNoSniffErrorInFile($report);
	}

	public function testFixableUnusedUses(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableUnusedUses.php', [], [UnusedUsesSniff::CODE_UNUSED_USE]);
		$this->assertAllFixedInFile($report);
	}

}
