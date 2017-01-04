<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHintHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function dataIsSimpleTypeHint(): array
	{
		return [
			['int', true],
			['integer', true],
			['float', true],
			['string', true],
			['bool', true],
			['boolean', true],
			['callable', true],
			['self', true],
			['array', true],
			['iterable', true],
			['void', true],

			['\Traversable', false],
			['resource', false],
			['mixed[]', false],
			['object', false],
			['null', false],
		];
	}

	/**
	 * @dataProvider dataIsSimpleTypeHint
	 * @param string $typeHint
	 * @param bool $isSimple
	 */
	public function testIsSimpleTypeHint(string $typeHint, bool $isSimple)
	{
		$this->assertSame($isSimple, TypeHintHelper::isSimpleTypeHint($typeHint));
	}

	public function dataIsSimpleIterableTypeHint(): array
	{
		return [
			['array', true],
			['iterable', true],

			['\Traversable', false],
			['mixed[]', false],
		];
	}

	/**
	 * @dataProvider dataIsSimpleIterableTypeHint
	 * @param string $typeHint
	 * @param bool $isSimple
	 */
	public function testIsSimpleIterableTypeHint(string $typeHint, bool $isSimple)
	{
		$this->assertSame($isSimple, TypeHintHelper::isSimpleIterableTypeHint($typeHint));
	}

	public function dataConvertLongSimpleTypeHintToShort(): array
	{
		return [
			['integer', 'int'],
			['boolean', 'bool'],

			['int', 'int'],
			['bool', 'bool'],
			['string', 'string'],
			['float', 'float'],
		];
	}

	/**
	 * @dataProvider dataConvertLongSimpleTypeHintToShort
	 * @param string $long
	 * @param string $short
	 */
	public function testConvertLongSimpleTypeHintToShort(string $long, string $short)
	{
		$this->assertSame($short, TypeHintHelper::convertLongSimpleTypeHintToShort($long));
	}

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
		$this->assertSame('\FooNamespace\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnTypeHint->getTypeHint()));
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
		$this->assertSame('bool', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnTypeHint->getTypeHint()));
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
		$this->assertSame('\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnTypeHint->getTypeHint()));
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
		$this->assertSame('bool', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnTypeHint->getTypeHint()));
	}

}
