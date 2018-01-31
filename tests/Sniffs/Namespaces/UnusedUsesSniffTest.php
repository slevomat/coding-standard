<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UnusedUsesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	private function getFileReport(): \PHP_CodeSniffer\Files\File
	{
		return self::checkFile(__DIR__ . '/data/unusedUses.php');
	}

	public function testUnusedUse(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			5,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'First\ObjectPrototype'
		);
		self::assertSniffError(
			$this->getFileReport(),
			12,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'FooBar\UnusedFunction'
		);
		self::assertSniffError(
			$this->getFileReport(),
			14,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'FooBar\UNUSED_CONSTANT'
		);
	}

	public function testUnusedUseNoNamespaceNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUsesNoNamespaceNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testUnusedUseWithAsPart(): void
	{
		self::assertSniffError(
			$this->getFileReport(),
			8,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'My\ObjectPrototype (as MyObject)'
		);
	}

	public function testUsedPartialNamespaceUse(): void
	{
		self::assertNoSniffError($this->getFileReport(), 6);
	}

	public function testUsedPartialSubnamespaceUse(): void
	{
		self::assertNoSniffError($this->getFileReport(), 7);
	}

	public function testUsedUseWithAsPart(): void
	{
		self::assertNoSniffError($this->getFileReport(), 9);
	}

	public function testUsedUseInTypeHint(): void
	{
		self::assertNoSniffError($this->getFileReport(), 10);
	}

	public function testUsedUseWithStaticMethodCall(): void
	{
		self::assertNoSniffError($this->getFileReport(), 11);
	}

	public function testUsedFunction(): void
	{
		self::assertNoSniffError($this->getFileReport(), 13);
	}

	public function testUsedConstant(): void
	{
		self::assertNoSniffError($this->getFileReport(), 15);
	}

	public function testUsedClassesInImplements(): void
	{
		self::assertNoSniffError($this->getFileReport(), 17);
		self::assertNoSniffError($this->getFileReport(), 18);
	}

	public function testReturnTypeHint(): void
	{
		self::assertNoSniffError($this->getFileReport(), 19);
	}

	public function testPartialUses(): void
	{
		self::assertNoSniffError($this->getFileReport(), 20);
		self::assertNoSniffError($this->getFileReport(), 21);
		self::assertNoSniffError($this->getFileReport(), 22);
		self::assertNoSniffError($this->getFileReport(), 23);
	}

	public function testUsedUseInAnnotationWithDisabledSearchAnnotations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUsesInAnnotation.php', [
			'searchAnnotations' => false,
		]);

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError(
			$report,
			5,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type Assert is not used in this file.'
		);
		self::assertSniffError(
			$report,
			6,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type Doctrine\ORM\Mapping (as ORM) is not used in this file.'
		);
		self::assertSniffError(
			$report,
			8,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type X is not used in this file.'
		);
		self::assertSniffError(
			$report,
			9,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type XX is not used in this file.'
		);
		self::assertSniffError(
			$report,
			10,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type XXX is not used in this file.'
		);
	}

	public function testUsedUseInAnnotationWithEnabledSearchAnnotations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUsesInAnnotation.php', [
			'searchAnnotations' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError(
			$report,
			9,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type XX is not used in this file.'
		);
	}

	public function testFindCaseInsensitiveUse(): void
	{
		$report = self::checkFile(__DIR__ . '/data/caseInsensitiveUse.php');
		self::assertNoSniffError($report, 3);
	}

	public function testReportCaseInsensitiveUse(): void
	{
		$report = self::checkFile(__DIR__ . '/data/caseInsensitiveUse.php');
		self::assertSniffError(
			$report,
			5,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name BAR and use statement Bar do not match'
		);
	}

	public function testUsedTrait(): void
	{
		$report = self::checkFile(__DIR__ . '/data/usedTrait.php');
		self::assertNoSniffError($report, 5);
	}

	public function testTypeWithUnderscoresInAnnotation(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUsesAnnotationUnderscores.php', [
			'searchAnnotations' => true,
		]);
		self::assertNoSniffError($report, 5);
	}

	public function testMatchingCaseOfUseAndClassConstant(): void
	{
		$report = self::checkFile(__DIR__ . '/data/matchingCaseOfUseAndClassConstant.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testMatchingCaseOfUseAndPhpFunction(): void
	{
		$report = self::checkFile(__DIR__ . '/data/matchingCaseOfUseAndPhpFunction.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testFixableUnusedUses(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableUnusedUses.php', [], [UnusedUsesSniff::CODE_UNUSED_USE]);
		self::assertAllFixedInFile($report);
	}

}
