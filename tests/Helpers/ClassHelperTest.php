<?php

namespace SlevomatCodingStandard\Helpers;

class ClassHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testNameWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithNamespace.php');
		$this->assertSame('\FooNamespace\FooClass', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		$this->assertSame('FooClass', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		$this->assertSame('\FooNamespace\FooInterface', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		$this->assertSame('FooInterface', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		$this->assertSame('\FooNamespace\FooTrait', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
		$this->assertSame('FooTrait', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
	}

	public function testNameWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithoutNamespace.php');
		$this->assertSame('\FooClass', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		$this->assertSame('FooClass', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooClass')));
		$this->assertSame('\FooInterface', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		$this->assertSame('FooInterface', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooInterface')));
		$this->assertSame('\FooTrait', ClassHelper::getFullyQualifiedName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
		$this->assertSame('FooTrait', ClassHelper::getName($codeSnifferFile, $this->findClassPointerByName($codeSnifferFile, 'FooTrait')));
	}

}
