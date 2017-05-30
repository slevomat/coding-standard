<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHintHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	/**
	 * @return mixed[][]
	 */
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

	/**
	 * @return mixed[][]
	 */
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

	/**
	 * @return string[][]
	 */
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

	public function testFunctionReturnAnnotationWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithReturnAnnotation');
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

	public function testFunctionParameterAnnotationWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer)[0];
		$parameterTypeHint = preg_split('~\\s+~', $parameterAnnotation->getContent())[0];
		$this->assertSame('\Doctrine\Common\Collections\ArrayCollection', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $parameterTypeHint));
	}

	public function testFunctionParameterTypeHintWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer)['$parameter'];
		$this->assertSame('\Doctrine\Common\Collections\ArrayCollection', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $parameterTypeHint->getTypeHint()));
	}

	public function testMethodReturnAnnotationWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithReturnAnnotation');
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

	public function testMethodParameterAnnotationWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($codeSnifferFile, $methodPointer)[0];
		$parameterTypeHint = preg_split('~\\s+~', $parameterAnnotation->getContent())[0];
		$this->assertSame('\Doctrine\ORM\Mapping\Id', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $parameterTypeHint));
	}

	public function testMethodParameterTypeHintWithNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($codeSnifferFile, $methodPointer)['$parameter'];
		$this->assertSame('\Doctrine\ORM\Mapping\Id', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $parameterTypeHint->getTypeHint()));
	}

	public function testFunctionReturnAnnotationWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithReturnAnnotation');
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

	public function testFunctionParameterAnnotationWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer)[0];
		$parameterTypeHint = preg_split('~\\s+~', $parameterAnnotation->getContent())[0];
		$this->assertSame('\Doctrine\Common\Collections\ArrayCollection', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $parameterTypeHint));
	}

	public function testFunctionParameterTypeHintWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer)['$parameter'];
		$this->assertSame('\Doctrine\Common\Collections\ArrayCollection', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $parameterTypeHint->getTypeHint()));
	}

	public function testMethodReturnAnnotationWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithReturnAnnotation');
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

	public function testMethodParameterAnnotationWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($codeSnifferFile, $methodPointer)[0];
		$parameterTypeHint = preg_split('~\\s+~', $parameterAnnotation->getContent())[0];
		$this->assertSame('\Doctrine\ORM\Mapping\Id', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $parameterTypeHint));
	}

	public function testMethodParameterTypeHintWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($codeSnifferFile, $methodPointer)['$parameter'];
		$this->assertSame('\Doctrine\ORM\Mapping\Id', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $parameterTypeHint->getTypeHint()));
	}

}
