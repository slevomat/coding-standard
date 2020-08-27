<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Sniffs\TestCase;

class UnusedUsesSniffTest extends TestCase
{

	public function testUnusedUse(): void
	{
		self::assertEquals(5, $this->getFileReport()->getErrorCount());
		self::assertSniffError(
			$this->getFileReport(),
			5,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'First\ObjectPrototype'
		);
		self::assertSniffError(
			$this->getFileReport(),
			8,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'My\ObjectPrototype (as MyObject)'
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
		self::assertSniffError(
			$this->getFileReport(),
			16,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'X'
		);
	}

	public function testUnusedUseWithMultipleNamespaces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUsesMultipleNamespaces.php');

		self::assertEquals(8, $report->getErrorCount());
		self::assertSniffError($report, 5, UnusedUsesSniff::CODE_UNUSED_USE, 'First\ObjectPrototype');
		self::assertSniffError($report, 12, UnusedUsesSniff::CODE_UNUSED_USE, 'FooBar\UnusedFunction');
		self::assertSniffError($report, 14, UnusedUsesSniff::CODE_UNUSED_USE, 'FooBar\UNUSED_CONSTANT');
		self::assertSniffError($report, 53, UnusedUsesSniff::CODE_UNUSED_USE, 'Human\Arm');
		self::assertSniffError($report, 55, UnusedUsesSniff::CODE_UNUSED_USE, 'Human\Leg');

		self::assertSniffError($report, 56, UnusedUsesSniff::CODE_UNUSED_USE, 'Human\HEAD_SIZE_UNUSED');
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

		self::assertSame(62, $report->getErrorCount());

		self::assertSniffError($report, 5, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Assert is not used in this file.');
		self::assertSniffError(
			$report,
			6,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type Doctrine\ORM\Mapping (as ORM) is not used in this file.'
		);
		self::assertSniffError($report, 8, UnusedUsesSniff::CODE_UNUSED_USE, 'Type X is not used in this file.');
		self::assertSniffError($report, 9, UnusedUsesSniff::CODE_UNUSED_USE, 'Type XX is not used in this file.');
		self::assertSniffError($report, 10, UnusedUsesSniff::CODE_UNUSED_USE, 'Type XXX is not used in this file.');
		self::assertSniffError(
			$report,
			11,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type Doctrine\Common\Collections\Collection is not used in this file.'
		);
		self::assertSniffError(
			$report,
			12,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type Doctrine\ORM\Mapping\Property is not used in this file.'
		);
		self::assertSniffError(
			$report,
			13,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type ProxyManager\Proxy\GhostObjectInterface is not used in this file.'
		);
		self::assertSniffError($report, 14, UnusedUsesSniff::CODE_UNUSED_USE, 'Type InvalidArgumentException is not used in this file.');
		self::assertSniffError($report, 15, UnusedUsesSniff::CODE_UNUSED_USE, 'Type LengthException is not used in this file.');
		self::assertSniffError($report, 16, UnusedUsesSniff::CODE_UNUSED_USE, 'Type RuntimeException is not used in this file.');
		self::assertSniffError(
			$report,
			17,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type Symfony\Component\Validator\Constraints (as Assert2) is not used in this file.'
		);
		self::assertSniffError($report, 18, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Foo\Boo\A is not used in this file.');
		self::assertSniffError($report, 19, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Foo\Boo\B is not used in this file.');
		self::assertSniffError($report, 20, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Foo\Boo\C is not used in this file.');
		self::assertSniffError($report, 21, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Foo\Boo\D is not used in this file.');
		self::assertSniffError($report, 22, UnusedUsesSniff::CODE_UNUSED_USE, 'Type InvalidAnnotation is not used in this file.');
		self::assertSniffError($report, 23, UnusedUsesSniff::CODE_UNUSED_USE, 'Type PropertyAnnotation is not used in this file.');
		self::assertSniffError($report, 24, UnusedUsesSniff::CODE_UNUSED_USE, 'Type PropertyReadAnnotation is not used in this file.');
		self::assertSniffError($report, 25, UnusedUsesSniff::CODE_UNUSED_USE, 'Type PropertyWriteAnnotation is not used in this file.');
		self::assertSniffError($report, 26, UnusedUsesSniff::CODE_UNUSED_USE, 'Type VarAnnotation is not used in this file.');
		self::assertSniffError($report, 27, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ParamAnnotation is not used in this file.');
		self::assertSniffError($report, 28, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ReturnAnnotation is not used in this file.');
		self::assertSniffError($report, 29, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ThrowsAnnotation is not used in this file.');
		self::assertSniffError($report, 30, UnusedUsesSniff::CODE_UNUSED_USE, 'Type MethodAnnotation is not used in this file.');
		self::assertSniffError($report, 31, UnusedUsesSniff::CODE_UNUSED_USE, 'Type MethodParameter1 is not used in this file.');
		self::assertSniffError($report, 32, UnusedUsesSniff::CODE_UNUSED_USE, 'Type MethodParameter2 is not used in this file.');
		self::assertSniffError($report, 33, UnusedUsesSniff::CODE_UNUSED_USE, 'Type MethodParameter3 is not used in this file.');
		self::assertSniffError($report, 34, UnusedUsesSniff::CODE_UNUSED_USE, 'Type MethodParameter4 is not used in this file.');
		self::assertSniffError($report, 35, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Discriminator\Lorem is not used in this file.');
		self::assertSniffError($report, 36, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Discriminator\Ipsum is not used in this file.');
		self::assertSniffError($report, 37, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Outer is not used in this file.');
		self::assertSniffError($report, 38, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Inner1 is not used in this file.');
		self::assertSniffError($report, 39, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Inner2 is not used in this file.');
		self::assertSniffError($report, 40, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Inner3 is not used in this file.');
		self::assertSniffError($report, 41, UnusedUsesSniff::CODE_UNUSED_USE, 'Type SeeWithMethod is not used in this file.');
		self::assertSniffError($report, 42, UnusedUsesSniff::CODE_UNUSED_USE, 'Type SeeWithoutMethod is not used in this file.');
		self::assertSniffError($report, 43, UnusedUsesSniff::CODE_UNUSED_USE, 'Type MethodReturn is not used in this file.');
		self::assertSniffError($report, 44, UnusedUsesSniff::CODE_UNUSED_USE, 'Type MethodReturnCollection is not used in this file.');
		self::assertSniffError($report, 45, UnusedUsesSniff::CODE_UNUSED_USE, 'Type MethodParameter5 is not used in this file.');
		self::assertSniffError($report, 46, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag1 is not used in this file.');
		self::assertSniffError($report, 47, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag2 is not used in this file.');
		self::assertSniffError($report, 48, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag3 is not used in this file.');
		self::assertSniffError($report, 49, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag4 is not used in this file.');
		self::assertSniffError($report, 50, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag5 is not used in this file.');
		self::assertSniffError($report, 51, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag6 is not used in this file.');
		self::assertSniffError($report, 52, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag7 is not used in this file.');
		self::assertSniffError($report, 53, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag8 is not used in this file.');
		self::assertSniffError($report, 54, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag9 is not used in this file.');
		self::assertSniffError($report, 55, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag10 is not used in this file.');
		self::assertSniffError($report, 56, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag11 is not used in this file.');
		self::assertSniffError($report, 57, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag12 is not used in this file.');
		self::assertSniffError($report, 58, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag13 is not used in this file.');
		self::assertSniffError($report, 59, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Iag14 is not used in this file.');
		self::assertSniffError($report, 60, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Callable1 is not used in this file.');
		self::assertSniffError($report, 61, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Callable2 is not used in this file.');
		self::assertSniffError($report, 62, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ArrayShape1 is not used in this file.');
		self::assertSniffError($report, 63, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ArrayShape2 is not used in this file.');
		self::assertSniffError($report, 64, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ConstantExpression1 is not used in this file.');
		self::assertSniffError($report, 65, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ConstantExpression2 is not used in this file.');
		self::assertSniffError($report, 66, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ConstantExpression3 is not used in this file.');
	}

	public function testUsedUseInAnnotationWithEnabledSearchAnnotations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUsesInAnnotation.php', [
			'searchAnnotations' => true,
		], [UnusedUsesSniff::CODE_UNUSED_USE]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 9, UnusedUsesSniff::CODE_UNUSED_USE, 'Type XX is not used in this file.');

		self::assertSniffError($report, 22, UnusedUsesSniff::CODE_UNUSED_USE, 'Type InvalidAnnotation is not used in this file.');
	}

	public function testReportCaseInsensitiveUse(): void
	{
		$report = self::checkFile(__DIR__ . '/data/caseInsensitiveUse.php', [
			'searchAnnotations' => true,
		], [UnusedUsesSniff::CODE_MISMATCHING_CASE]);

		self::assertSame(8, $report->getErrorCount());

		self::assertSniffError(
			$report,
			30,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "bar" and use statement "Bar" does not match'
		);
		self::assertSniffError(
			$report,
			32,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "BAR" and use statement "Bar" does not match'
		);

		self::assertSniffError(
			$report,
			34,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "boo" and use statement "Boo" does not match'
		);
		self::assertSniffError(
			$report,
			35,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "BOO" and use statement "Boo" does not match'
		);
		self::assertSniffError(
			$report,
			37,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "boo" and use statement "Boo" does not match'
		);

		self::assertSniffError(
			$report,
			79,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "ignore" and use statement "Ignore" does not match'
		);

		self::assertSniffError(
			$report,
			79,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "uuid" and use statement "Uuid" does not match'
		);

		self::assertSniffError(
			$report,
			107,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "ignore" and use statement "Ignore" does not match'
		);
	}

	public function testIgnoredAnnotationsAreNotUsed(): void
	{
		$report = self::checkFile(__DIR__ . '/data/caseInsensitiveUse.php', [
			'searchAnnotations' => true,
			'ignoredAnnotationNames' => ['@ignore'],
			'ignoredAnnotations' => ['@group'],
		], [UnusedUsesSniff::CODE_UNUSED_USE]);

		self::assertSniffError($report, 8, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Ignore is not used in this file.');
	}

	public function testIgnoredAnnotations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/caseInsensitiveUse.php', [
			'searchAnnotations' => true,
			'ignoredAnnotationNames' => ['@ignore'],
			'ignoredAnnotations' => ['@group'],
		], [UnusedUsesSniff::CODE_MISMATCHING_CASE]);

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError(
			$report,
			30,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "bar" and use statement "Bar" does not match'
		);
		self::assertSniffError(
			$report,
			32,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "BAR" and use statement "Bar" does not match'
		);

		self::assertSniffError(
			$report,
			34,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "boo" and use statement "Boo" does not match'
		);
		self::assertSniffError(
			$report,
			35,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "BOO" and use statement "Boo" does not match'
		);
		self::assertSniffError(
			$report,
			37,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "boo" and use statement "Boo" does not match'
		);

		self::assertSniffError(
			$report,
			79,
			UnusedUsesSniff::CODE_MISMATCHING_CASE,
			'Case of reference name "uuid" and use statement "Uuid" does not match'
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

	public function testNoUsesWithSearchInAnnotationsEnabledNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noUses.php', [
			'searchAnnotations' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testUseAfterOpeningTag(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUsesUseAfterOpen.php', [
			'searchAnnotations' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	private function getFileReport(): File
	{
		return self::checkFile(__DIR__ . '/data/unusedUses.php');
	}

}
