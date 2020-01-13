<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function array_values;
use const T_ANON_CLASS;

class ClassHelperTest extends TestCase
{

	public function testNameWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNamespace.php');
		self::assertSame('\FooNamespace\FooClass', ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooClass')));
		self::assertSame('FooClass', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooClass')));
		self::assertSame('\FooNamespace\FooInterface', ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooInterface')));
		self::assertSame('FooInterface', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooInterface')));
		self::assertSame('\FooNamespace\FooTrait', ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooTrait')));
		self::assertSame('FooTrait', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooTrait')));
		self::assertSame('class@anonymous', ClassHelper::getFullyQualifiedName($phpcsFile, $this->findPointerByLineAndType($phpcsFile, 20, T_ANON_CLASS)));
		self::assertSame('class@anonymous', ClassHelper::getName($phpcsFile, $this->findPointerByLineAndType($phpcsFile, 20, T_ANON_CLASS)));
	}

	public function testNameWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithoutNamespace.php');
		self::assertSame('\FooClass', ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooClass')));
		self::assertSame('FooClass', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooClass')));
		self::assertSame('\FooInterface', ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooInterface')));
		self::assertSame('FooInterface', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooInterface')));
		self::assertSame('\FooTrait', ClassHelper::getFullyQualifiedName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooTrait')));
		self::assertSame('FooTrait', ClassHelper::getName($phpcsFile, $this->findClassPointerByName($phpcsFile, 'FooTrait')));
		self::assertSame('class@anonymous', ClassHelper::getFullyQualifiedName($phpcsFile, $this->findPointerByLineAndType($phpcsFile, 18, T_ANON_CLASS)));
		self::assertSame('class@anonymous', ClassHelper::getName($phpcsFile, $this->findPointerByLineAndType($phpcsFile, 18, T_ANON_CLASS)));
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

}
