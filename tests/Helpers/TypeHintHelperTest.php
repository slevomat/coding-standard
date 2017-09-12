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
			['object', PHP_VERSION_ID >= 70200],
			['null', false],
		];
	}

	/**
	 * @dataProvider dataIsSimpleTypeHint
	 * @param string $typeHint
	 * @param bool $isSimple
	 */
	public function testIsSimpleTypeHint(string $typeHint, bool $isSimple): void
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
	public function testIsSimpleIterableTypeHint(string $typeHint, bool $isSimple): void
	{
		$this->assertSame($isSimple, TypeHintHelper::isSimpleIterableTypeHint($typeHint));
	}

	/**
	 * @return mixed[][]
	 */
	public function dataIsSimpleUnofficialTypeHint(): array
	{
		return [
			['null', true],
			['mixed', true],
			['true', true],
			['false', true],
			['resource', true],
			['static', true],
			['$this', true],

			['\Traversable', false],
			['int', false],
			['bool', false],
			['object', PHP_VERSION_ID < 70200],
			['string', false],
		];
	}

	/**
	 * @dataProvider dataIsSimpleUnofficialTypeHint
	 * @param string $typeHint
	 * @param bool $isSimple
	 */
	public function testIsSimpleUnofficialTypeHint(string $typeHint, bool $isSimple): void
	{
		$this->assertSame($isSimple, TypeHintHelper::isSimpleUnofficialTypeHints($typeHint));
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
	public function testConvertLongSimpleTypeHintToShort(string $long, string $short): void
	{
		$this->assertSame($short, TypeHintHelper::convertLongSimpleTypeHintToShort($long));
	}

	public function testFunctionReturnAnnotationWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithReturnAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer);
		$this->assertSame('void', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnAnnotation->getContent()));
	}

	public function testFunctionReturnTypeHintWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer);
		$this->assertSame('\FooNamespace\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnTypeHint->getTypeHint()));
	}

	public function testFunctionParameterAnnotationWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer)[0];
		$parameterTypeHint = preg_split('~\\s+~', $parameterAnnotation->getContent())[0];
		$this->assertSame('\Doctrine\Common\Collections\ArrayCollection', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $parameterTypeHint));
	}

	public function testFunctionParameterTypeHintWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer)['$parameter'];
		$this->assertSame('\Doctrine\Common\Collections\ArrayCollection', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $parameterTypeHint->getTypeHint()));
	}

	public function testMethodReturnAnnotationWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithReturnAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($codeSnifferFile, $methodPointer);
		$this->assertSame('\FooNamespace\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnAnnotation->getContent()));
	}

	public function testMethodReturnTypeHintWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $methodPointer);
		$this->assertSame('bool', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnTypeHint->getTypeHint()));
	}

	public function testMethodParameterAnnotationWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($codeSnifferFile, $methodPointer)[0];
		$parameterTypeHint = preg_split('~\\s+~', $parameterAnnotation->getContent())[0];
		$this->assertSame('\Doctrine\ORM\Mapping\Id', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $parameterTypeHint));
	}

	public function testMethodParameterTypeHintWithNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($codeSnifferFile, $methodPointer)['$parameter'];
		$this->assertSame('\Doctrine\ORM\Mapping\Id', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $parameterTypeHint->getTypeHint()));
	}

	public function testFunctionReturnAnnotationWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithReturnAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($codeSnifferFile, $functionPointer);
		$this->assertSame('void', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnAnnotation->getContent()));
	}

	public function testFunctionReturnTypeHintWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $functionPointer);
		$this->assertSame('\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $returnTypeHint->getTypeHint()));
	}

	public function testFunctionParameterAnnotationWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($codeSnifferFile, $functionPointer)[0];
		$parameterTypeHint = preg_split('~\\s+~', $parameterAnnotation->getContent())[0];
		$this->assertSame('\Doctrine\Common\Collections\ArrayCollection', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $parameterTypeHint));
	}

	public function testFunctionParameterTypeHintWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooFunctionWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($codeSnifferFile, $functionPointer)['$parameter'];
		$this->assertSame('\Doctrine\Common\Collections\ArrayCollection', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $functionPointer, $parameterTypeHint->getTypeHint()));
	}

	public function testMethodReturnAnnotationWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithReturnAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($codeSnifferFile, $methodPointer);
		$this->assertSame('\FooClass', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnAnnotation->getContent()));
	}

	public function testMethodReturnTypeHintWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($codeSnifferFile, $methodPointer);
		$this->assertSame('bool', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $returnTypeHint->getTypeHint()));
	}

	public function testMethodParameterAnnotationWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($codeSnifferFile, $methodPointer)[0];
		$parameterTypeHint = preg_split('~\\s+~', $parameterAnnotation->getContent())[0];
		$this->assertSame('\Doctrine\ORM\Mapping\Id', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $parameterTypeHint));
	}

	public function testMethodParameterTypeHintWithoutNamespace(): void
	{
		$codeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($codeSnifferFile, 'fooMethodWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($codeSnifferFile, $methodPointer)['$parameter'];
		$this->assertSame('\Doctrine\ORM\Mapping\Id', TypeHintHelper::getFullyQualifiedTypeHint($codeSnifferFile, $methodPointer, $parameterTypeHint->getTypeHint()));
	}

}
