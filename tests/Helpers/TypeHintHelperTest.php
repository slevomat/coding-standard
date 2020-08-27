<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function preg_split;
use const T_DOC_COMMENT_OPEN_TAG;

class TypeHintHelperTest extends TestCase
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
	public function testIsSimpleTypeHint(string $typeHint, bool $isSimple): void
	{
		self::assertSame($isSimple, TypeHintHelper::isSimpleTypeHint($typeHint));
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
		self::assertSame($isSimple, TypeHintHelper::isSimpleIterableTypeHint($typeHint));
	}

	/**
	 * @return mixed[][]
	 */
	public function dataIsSimpleUnofficialTypeHint(): array
	{
		return [
			['null', true],
			['mixed', true],
			['scalar', true],
			['numeric', true],
			['true', true],
			['false', true],
			['resource', true],
			['static', true],
			['$this', true],

			['\Traversable', false],
			['int', false],
			['bool', false],
			['object', true],
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
		self::assertSame($isSimple, TypeHintHelper::isSimpleUnofficialTypeHints($typeHint));
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
		self::assertSame($short, TypeHintHelper::convertLongSimpleTypeHintToShort($long));
	}

	public function testFunctionReturnAnnotationWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'fooFunctionWithReturnAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
		self::assertSame('void', TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnAnnotation->getContent()));
	}

	public function testFunctionReturnTypeHintWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'fooFunctionWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame(
			'\FooNamespace\FooClass',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint())
		);
	}

	public function testFunctionParameterAnnotationWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'fooFunctionWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer)[0];
		$parts = preg_split('~\\s+~', $parameterAnnotation->getContent());
		self::assertIsArray($parts);
		$parameterTypeHint = $parts[0];
		self::assertSame(
			'\Doctrine\Common\Collections\ArrayCollection',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHint)
		);
	}

	public function testFunctionParameterTypeHintWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'fooFunctionWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer)['$parameter'];
		self::assertSame(
			'\Doctrine\Common\Collections\ArrayCollection',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint())
		);
	}

	public function testMethodReturnAnnotationWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($phpcsFile, 'fooMethodWithReturnAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $methodPointer);
		self::assertSame(
			'\FooNamespace\FooClass',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, $returnAnnotation->getContent())
		);
	}

	public function testMethodReturnTypeHintWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($phpcsFile, 'fooMethodWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $methodPointer);
		self::assertSame('bool', TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, $returnTypeHint->getTypeHint()));
	}

	public function testMethodParameterAnnotationWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($phpcsFile, 'fooMethodWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($phpcsFile, $methodPointer)[0];
		$parts = preg_split('~\\s+~', $parameterAnnotation->getContent());
		self::assertIsArray($parts);
		$parameterTypeHint = $parts[0];
		self::assertSame(
			'\Doctrine\ORM\Mapping\Id',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, $parameterTypeHint)
		);
	}

	public function testMethodParameterTypeHintWithNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($phpcsFile, 'fooMethodWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($phpcsFile, $methodPointer)['$parameter'];
		self::assertSame(
			'\Doctrine\ORM\Mapping\Id',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, $parameterTypeHint->getTypeHint())
		);
	}

	public function testFunctionReturnAnnotationWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'fooFunctionWithReturnAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
		self::assertSame('void', TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnAnnotation->getContent()));
	}

	public function testFunctionReturnTypeHintWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'fooFunctionWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		self::assertSame(
			'\FooClass',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint())
		);
	}

	public function testFunctionParameterAnnotationWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'fooFunctionWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer)[0];
		$parts = preg_split('~\\s+~', $parameterAnnotation->getContent());
		self::assertIsArray($parts);
		$parameterTypeHint = $parts[0];
		self::assertSame(
			'\Doctrine\Common\Collections\ArrayCollection',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHint)
		);
	}

	public function testFunctionParameterTypeHintWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, 'fooFunctionWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer)['$parameter'];
		self::assertSame(
			'\Doctrine\Common\Collections\ArrayCollection',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint())
		);
	}

	public function testMethodReturnAnnotationWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($phpcsFile, 'fooMethodWithReturnAnnotation');
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $methodPointer);
		self::assertSame(
			'\FooClass',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, $returnAnnotation->getContent())
		);
	}

	public function testMethodReturnTypeHintWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($phpcsFile, 'fooMethodWithReturnTypeHint');
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $methodPointer);
		self::assertSame('bool', TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, $returnTypeHint->getTypeHint()));
	}

	public function testMethodParameterAnnotationWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($phpcsFile, 'fooMethodWithParameterAnnotation');
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($phpcsFile, $methodPointer)[0];
		$parts = preg_split('~\\s+~', $parameterAnnotation->getContent());
		self::assertIsArray($parts);
		$parameterTypeHint = $parts[0];
		self::assertSame(
			'\Doctrine\ORM\Mapping\Id',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, $parameterTypeHint)
		);
	}

	public function testMethodParameterTypeHintWithoutNamespace(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintWithoutNamespace.php');

		$methodPointer = $this->findFunctionPointerByName($phpcsFile, 'fooMethodWithParameterTypeHint');
		$parameterTypeHint = FunctionHelper::getParametersTypeHints($phpcsFile, $methodPointer)['$parameter'];
		self::assertSame(
			'\Doctrine\ORM\Mapping\Id',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, $parameterTypeHint->getTypeHint())
		);
	}

	/**
	 * @return mixed[][]
	 */
	public function dataIsTemplate(): array
	{
		return [
			['Whatever', false],
		];
	}

	/**
	 * @dataProvider dataIsTemplate
	 * @param string $typeHintName
	 * @param bool $isTemplate
	 */
	public function testIsTemplate(string $typeHintName, bool $isTemplate): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintTemplates.php');

		$docCommentOpenPointer = $this->findPointerByLineAndType($phpcsFile, 3, T_DOC_COMMENT_OPEN_TAG);

		self::assertNotNull($docCommentOpenPointer);

		self::assertSame($isTemplate, TypeHintHelper::isTemplate($phpcsFile, $docCommentOpenPointer, $typeHintName));
	}

}
