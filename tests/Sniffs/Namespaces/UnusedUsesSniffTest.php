<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class UnusedUsesSniffTest extends TestCase
{

	public function testUnusedUse(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUses.php');

		self::assertEquals(5, $report->getErrorCount());

		self::assertSniffError($report, 5, UnusedUsesSniff::CODE_UNUSED_USE, 'First\ObjectPrototype');

		// Partial namespace use
		self::assertNoSniffError($report, 6);

		// Used partial subnamespace
		self::assertNoSniffError($report, 7);

		self::assertSniffError($report, 8, UnusedUsesSniff::CODE_UNUSED_USE, 'My\ObjectPrototype (as MyObject)');

		// Use with "as" part
		self::assertNoSniffError($report, 9);

		// Used in type hint
		self::assertNoSniffError($report, 10);

		// Static method call
		self::assertNoSniffError($report, 11);

		self::assertSniffError($report, 12, UnusedUsesSniff::CODE_UNUSED_USE, 'FooBar\UnusedFunction');

		// Used function
		self::assertNoSniffError($report, 13);

		self::assertSniffError($report, 14, UnusedUsesSniff::CODE_UNUSED_USE, 'FooBar\UNUSED_CONSTANT');

		// Used constant
		self::assertNoSniffError($report, 15);

		self::assertSniffError($report, 16, UnusedUsesSniff::CODE_UNUSED_USE, 'X');

		// Classes in implements
		self::assertNoSniffError($report, 17);
		self::assertNoSniffError($report, 18);

		// Return type hint
		self::assertNoSniffError($report, 19);

		// Partial uses
		self::assertNoSniffError($report, 20);
		self::assertNoSniffError($report, 21);
		self::assertNoSniffError($report, 22);
		self::assertNoSniffError($report, 23);

		// Used constant as named parameter
		self::assertNoSniffError($report, 26);

		// Used class::constant as named parameter
		self::assertNoSniffError($report, 27);
		self::assertNoSniffError($report, 28);

		// Used class with static variable
		self::assertNoSniffError($report, 29);

		self::assertNoSniffError($report, 30);

		self::assertNoSniffError($report, 91);
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

	public function testUsedUseInAnnotationWithDisabledSearchAnnotations(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUsesInAnnotation.php', [
			'searchAnnotations' => false,
		]);

		self::assertSame(86, $report->getErrorCount());

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
		self::assertSniffError($report, 67, UnusedUsesSniff::CODE_UNUSED_USE, 'Type TypeAlias1 is not used in this file.');
		self::assertSniffError($report, 68, UnusedUsesSniff::CODE_UNUSED_USE, 'Type TypeAlias2 is not used in this file.');
		self::assertSniffError($report, 69, UnusedUsesSniff::CODE_UNUSED_USE, 'Type SomeImportFrom1 is not used in this file.');
		self::assertSniffError($report, 70, UnusedUsesSniff::CODE_UNUSED_USE, 'Type SomeImportFrom2 is not used in this file.');
		self::assertSniffError($report, 71, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ArrayValue is not used in this file.');
		self::assertSniffError($report, 72, UnusedUsesSniff::CODE_UNUSED_USE, 'Type TypeAliasWithConstant is not used in this file.');
		self::assertSniffError($report, 73, UnusedUsesSniff::CODE_UNUSED_USE, 'Type CustomAssert is not used in this file.');
		self::assertSniffError($report, 74, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Conditional1 is not used in this file.');
		self::assertSniffError($report, 75, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Conditional2 is not used in this file.');
		self::assertSniffError($report, 76, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Conditional3 is not used in this file.');
		self::assertSniffError($report, 77, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Conditional4 is not used in this file.');
		self::assertSniffError($report, 78, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Conditional5 is not used in this file.');
		self::assertSniffError($report, 79, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Conditional6 is not used in this file.');
		self::assertSniffError($report, 80, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Conditional7 is not used in this file.');
		self::assertSniffError($report, 81, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Conditional8 is not used in this file.');
		self::assertSniffError($report, 82, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Conditional9 is not used in this file.');
		self::assertSniffError($report, 84, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Comma\After is not used in this file.');
		self::assertSniffError($report, 85, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ObjectShapeItem1 is not used in this file.');
		self::assertSniffError($report, 86, UnusedUsesSniff::CODE_UNUSED_USE, 'Type ObjectShapeItem2 is not used in this file.');
		self::assertSniffError($report, 87, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Doctrine\ORM\Mapping\Column is not used in this file.');
		self::assertSniffError($report, 88, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Doctrine\ORM\Mapping\Entity is not used in this file.');
		self::assertSniffError(
			$report,
			89,
			UnusedUsesSniff::CODE_UNUSED_USE,
			'Type Doctrine\ORM\Mapping\GeneratedValue is not used in this file.'
		);
		self::assertSniffError($report, 90, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Doctrine\ORM\Mapping\Id is not used in this file.');
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

	public function testIgnoredAnnotationsAreNotUsed(): void
	{
		$report = self::checkFile(__DIR__ . '/data/caseInsensitiveUse.php', [
			'searchAnnotations' => true,
			'ignoredAnnotationNames' => ['@ignore'],
			'ignoredAnnotations' => ['@group'],
		], [UnusedUsesSniff::CODE_UNUSED_USE]);

		self::assertSniffError($report, 8, UnusedUsesSniff::CODE_UNUSED_USE, 'Type Ignore is not used in this file.');
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

	public function testUsesInAttributes(): void
	{
		$report = self::checkFile(__DIR__ . '/data/usesInAttributes.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testUnusedUseNamespaceOperatorNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/unusedUsesNamespaceOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

}
