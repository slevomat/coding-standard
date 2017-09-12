<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ClassHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testNameWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNamespace.php');
		$this->assertSame('\FooNamespace\FooClass', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		$this->assertSame('FooClass', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		$this->assertSame('\FooNamespace\FooInterface', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		$this->assertSame('FooInterface', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		$this->assertSame('\FooNamespace\FooTrait', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
		$this->assertSame('FooTrait', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
	}

	public function testNameWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithoutNamespace.php');
		$this->assertSame('\FooClass', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		$this->assertSame('FooClass', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		$this->assertSame('\FooInterface', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		$this->assertSame('FooInterface', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		$this->assertSame('\FooTrait', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
		$this->assertSame('FooTrait', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
	}

	public function testGetAllNamesWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNamespace.php');
		$this->assertSame(['FooClass', 'FooInterface', 'FooTrait'], ClassHelper::getAllNames($codeSnifferFile));
	}

	public function testGetAllNamesWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithoutNamespace.php');
		$this->assertSame(['FooClass', 'FooInterface', 'FooTrait'], ClassHelper::getAllNames($codeSnifferFile));
	}

	public function testGetAllNamesWithNoClass(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/namespacedFile.php');
		$this->assertSame([], ClassHelper::getAllNames($codeSnifferFile));
	}

}
