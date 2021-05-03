<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function array_values;
use const T_ANON_CLASS;
use const T_USE;
use const T_WHITESPACE;

class ClassHelperTest extends TestCase
{

	public function testNameWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNamespace.php');
		self::assertSame(
			'\FooNamespace\FooClass',
			ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooClass'))
		);
		self::assertSame('FooClass', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooClass')));
		self::assertSame(
			'\FooNamespace\FooInterface',
			ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooInterface'))
		);
		self::assertSame('FooInterface', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooInterface')));
		self::assertSame(
			'\FooNamespace\FooTrait',
			ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooTrait'))
		);
		self::assertSame('FooTrait', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooTrait')));
		self::assertSame(
			'class@anonymous',
			ClassHelper::getFullyQualifiedName($phpcsFile, $this->findPointerByLineAndType($phpcsFile, 20, T_ANON_CLASS))
		);
		self::assertSame(
			'class@anonymous',
			ClassHelper::getName($phpcsFile, $this->findPointerByLineAndType($phpcsFile, 20, T_ANON_CLASS))
		);
	}

	public function testNameWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithoutNamespace.php');
		self::assertSame(
			'\FooClass',
			ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooClass'))
		);
		self::assertSame('FooClass', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooClass')));
		self::assertSame(
			'\FooInterface',
			ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooInterface'))
		);
		self::assertSame('FooInterface', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooInterface')));
		self::assertSame(
			'\FooTrait',
			ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooTrait'))
		);
		self::assertSame('FooTrait', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooTrait')));
		self::assertSame(
			'class@anonymous',
			ClassHelper::getFullyQualifiedName($phpcsFile, $this->findPointerByLineAndType($phpcsFile, 18, T_ANON_CLASS))
		);
		self::assertSame(
			'class@anonymous',
			ClassHelper::getName($phpcsFile, $this->findPointerByLineAndType($phpcsFile, 18, T_ANON_CLASS))
		);
	}

	public function testGetAllNamesWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNamespace.php');
		self::assertSame(['FooClass', 'FooInterface', 'FooTrait'], array_values(ClassHelper::getAllNames($phpcsFile)));
	}

	public function testGetAllNamesWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithoutNamespace.php');
		self::assertSame(['FooClass', 'FooInterface', 'FooTrait'], array_values(ClassHelper::getAllNames($phpcsFile)));
	}

	public function testGetAllNamesWithNoClass(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/namespacedFile.php');
		self::assertSame([], ClassHelper::getAllNames($phpcsFile));
	}

	public function testGetClassPointerWithoutClass(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/namespacedFile.php');

		$usePointer = $this->findPointerByLineAndType($phpcsFile, 5, T_USE);
		self::assertNotNull($usePointer);
		self::assertNull(ClassHelper::getClassPointer($phpcsFile, $usePointer));
	}

	public function testGetClassPointerWithClass(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithoutNamespace.php');

		$whitespacePointer = $this->findPointerByLineAndType($phpcsFile, 5, T_WHITESPACE);
		self::assertNotNull($whitespacePointer);
		self::assertSame(2, ClassHelper::getClassPointer($phpcsFile, $whitespacePointer));
	}

	public function testGetClassPointerWithMultipleClasses(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/multipleClasses.php');

		$methodInFooPointer = $this->findFunctionPointerByName($phpcsFile, 'methodInFoo');
		self::assertNotNull($methodInFooPointer);
		self::assertSame(2, ClassHelper::getClassPointer($phpcsFile, $methodInFooPointer));

		$methodInBarPointer = $this->findFunctionPointerByName($phpcsFile, 'methodInBar');
		self::assertNotNull($methodInBarPointer);
		self::assertSame(28, ClassHelper::getClassPointer($phpcsFile, $methodInBarPointer));
	}

	public function testGetMethodPointersWithMultipleClasses(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/multipleClasses.php');

		$fooPointer = $this->findClassPointerByName($phpcsFile, 'Foo');
		$methodInFooPointer = $this->findFunctionPointerByName($phpcsFile, 'methodInFoo');
		self::assertSame([$methodInFooPointer], ClassHelper::getMethodPointers($phpcsFile, $fooPointer));

		$barPointer = $this->findClassPointerByName($phpcsFile, 'Bar');
		$methodInBarPointer = $this->findFunctionPointerByName($phpcsFile, 'methodInBar');
		self::assertSame([$methodInBarPointer], ClassHelper::getMethodPointers($phpcsFile, $barPointer));
	}

	public function testGetMethodPointersWithClassWithNestedAnonymousClass(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNestedAnonymousClass.php');

		$fooPointer = $this->findClassPointerByName($phpcsFile, 'Foo');
		$classMethodPointer = $this->findFunctionPointerByName($phpcsFile, 'classMethod');
		self::assertSame([$classMethodPointer], ClassHelper::getMethodPointers($phpcsFile, $fooPointer));

		$anonymousClassPointer = $this->findPointerByLineAndType($phpcsFile, 8, T_ANON_CLASS);
		$anonymousClassMethodPointer = $this->findFunctionPointerByName($phpcsFile, 'anonymousClassMethod');
		self::assertSame([$anonymousClassMethodPointer], ClassHelper::getMethodPointers($phpcsFile, $anonymousClassPointer));
	}

	public function testGetMethodPointersWithClassWithNestedFunction(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNestedFunction.php');

		$fooPointer = $this->findClassPointerByName($phpcsFile, 'Foo');
		$classMethodPointer = $this->findFunctionPointerByName($phpcsFile, 'classMethod');
		self::assertSame([$classMethodPointer], ClassHelper::getMethodPointers($phpcsFile, $fooPointer));
	}

}
