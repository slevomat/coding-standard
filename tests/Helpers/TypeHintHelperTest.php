<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHintHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testFunctionAnnotationTypeHintWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer);
		$this->assertSame('void', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnAnnotation->getContent()));
	}

	public function testFunctionReturnTypeHintWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer);
		$this->assertSame('\FooNamespace\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnTypeHint));
	}

	public function testMethodAnnotationTypeHintWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($codeSnifferFile, $methodPointer);
		$this->assertSame('\FooNamespace\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnAnnotation->getContent()));
	}

	public function testMethodReturnTypeHintWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $methodPointer);
		$this->assertSame('bool', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnTypeHint));
	}

	public function testFunctionAnnotationTypeHintWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer);
		$this->assertSame('void', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnAnnotation->getContent()));
	}

	public function testFunctionReturnTypeHintWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer);
		$this->assertSame('\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnTypeHint));
	}

	public function testMethodAnnotationTypeHintWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($codeSnifferFile, $methodPointer);
		$this->assertSame('\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnAnnotation->getContent()));
	}

	public function testMethodReturnTypeHintWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $methodPointer);
		$this->assertSame('bool', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnTypeHint));
	}

}
