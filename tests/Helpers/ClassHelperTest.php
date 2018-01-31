<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ClassHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testNameWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNamespace.php');
		self::assertSame('\FooNamespace\FooClass', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		self::assertSame('FooClass', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		self::assertSame('\FooNamespace\FooInterface', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		self::assertSame('FooInterface', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		self::assertSame('\FooNamespace\FooTrait', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
		self::assertSame('FooTrait', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
	}

	public function testNameWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithoutNamespace.php');
		self::assertSame('\FooClass', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		self::assertSame('FooClass', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		self::assertSame('\FooInterface', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		self::assertSame('FooInterface', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		self::assertSame('\FooTrait', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
		self::assertSame('FooTrait', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
	}

	public function testGetAllNamesWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNamespace.php');
		self::assertSame(['FooClass', 'FooInterface', 'FooTrait'], ClassHelper::getAllNames($codeSnifferFile));
	}

	public function testGetAllNamesWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithoutNamespace.php');
		self::assertSame(['FooClass', 'FooInterface', 'FooTrait'], ClassHelper::getAllNames($codeSnifferFile));
	}

	public function testGetAllNamesWithNoClass(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/namespacedFile.php');
		self::assertSame([], ClassHelper::getAllNames($codeSnifferFile));
	}

}
