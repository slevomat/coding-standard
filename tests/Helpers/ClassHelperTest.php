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

}
