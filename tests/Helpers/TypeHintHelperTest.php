<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use const T_DOC_COMMENT_OPEN_TAG;

class TypeHintHelperTest extends TestCase
{

	/**
	 * @return list<array{0: string, 1: bool}>
	 */
	public static function dataIsSimpleTypeHint(): array
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
	 */
	public function testIsSimpleTypeHint(string $typeHint, bool $isSimple): void
	{
		self::assertSame($isSimple, TypeHintHelper::isSimpleTypeHint($typeHint));
	}

	/**
	 * @return list<array{0: string, 1: bool}>
	 */
	public static function dataIsSimpleIterableTypeHint(): array
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
	 */
	public function testIsSimpleIterableTypeHint(string $typeHint, bool $isSimple): void
	{
		self::assertSame($isSimple, TypeHintHelper::isSimpleIterableTypeHint($typeHint));
	}

	/**
	 * @return list<array{0: string, 1: bool}>
	 */
	public static function dataIsSimpleUnofficialTypeHint(): array
	{
		return [
			['null', true],
			['mixed', true],
			['scalar', true],
			['numeric', true],
			['true', true],
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
	 */
	public function testIsSimpleUnofficialTypeHint(string $typeHint, bool $isSimple): void
	{
		self::assertSame($isSimple, TypeHintHelper::isSimpleUnofficialTypeHints($typeHint));
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	public static function dataConvertLongSimpleTypeHintToShort(): array
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
		self::assertSame(
			'void',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, (string) $returnAnnotation->getValue()->type)
		);
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
		/** @var Annotation<ParamTagValueNode> $parameterAnnotation */
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer)[0];

		self::assertSame(
			'\Doctrine\Common\Collections\ArrayCollection',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, (string) $parameterAnnotation->getValue()->type)
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
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, (string) $returnAnnotation->getValue()->type)
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
		/** @var Annotation<ParamTagValueNode> $parameterAnnotation */
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($phpcsFile, $methodPointer)[0];

		self::assertSame(
			'\Doctrine\ORM\Mapping\Id',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, (string) $parameterAnnotation->getValue()->type)
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
		self::assertSame(
			'void',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, (string) $returnAnnotation->getValue()->type)
		);
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
		/** @var Annotation<ParamTagValueNode> $parameterAnnotation */
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer)[0];

		self::assertSame(
			'\Doctrine\Common\Collections\ArrayCollection',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, (string) $parameterAnnotation->getValue()->type)
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
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, (string) $returnAnnotation->getValue()->type)
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
		/** @var Annotation<ParamTagValueNode> $parameterAnnotation */
		$parameterAnnotation = FunctionHelper::getParametersAnnotations($phpcsFile, $methodPointer)[0];

		self::assertSame(
			'\Doctrine\ORM\Mapping\Id',
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $methodPointer, (string) $parameterAnnotation->getValue()->type)
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
	 * @return list<array{0: string, 1: bool}>
	 */
	public static function dataIsTypeDefinedInAnnotation(): array
	{
		return [
			['Whatever', false],
		];
	}

	/**
	 * @dataProvider dataIsTypeDefinedInAnnotation
	 */
	public function testIsTypeDefinedInAnnotation(string $typeHintName, bool $isTemplate): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintDefinedInAnnotation.php');

		$docCommentOpenPointer = $this->findPointerByLineAndType($phpcsFile, 3, T_DOC_COMMENT_OPEN_TAG);

		self::assertNotNull($docCommentOpenPointer);

		self::assertSame($isTemplate, TypeHintHelper::isTypeDefinedInAnnotation($phpcsFile, $docCommentOpenPointer, $typeHintName));
	}

	/**
	 * @return list<array{0: int, 1: string, 2: bool}>
	 */
	public static function dataIsTypeDefinedInAnnotationWhenAnnotationIsInvalid(): array
	{
		return [
			[22, 'Alias', true],
			[29, 'Template', false],
		];
	}

	/**
	 * @dataProvider dataIsTypeDefinedInAnnotationWhenAnnotationIsInvalid
	 */
	public function testIsTypeDefinedInAnnotationWhenAnnotationIsInvalid(int $line, string $type, bool $isDefined): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintDefinedInAnnotation.php');

		$docCommentOpenPointer = $this->findPointerByLineAndType($phpcsFile, $line, T_DOC_COMMENT_OPEN_TAG);

		self::assertNotNull($docCommentOpenPointer);

		self::assertSame($isDefined, TypeHintHelper::isTypeDefinedInAnnotation($phpcsFile, $docCommentOpenPointer, $type));
	}

	/**
	 * @return list<array{0: string, 1: bool}>
	 */
	public static function dataTypeHintEqualsAnnotation(): array
	{
		return [
			['scalar', true],
			['unionIsNotIntersection', false],
		];
	}

	/**
	 * @dataProvider dataTypeHintEqualsAnnotation
	 */
	public function testTypeHintEqualsAnnotation(string $functionName, bool $equals): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/typeHintEqualsAnnotation.php');

		$functionPointer = $this->findFunctionPointerByName($phpcsFile, $functionName);

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);

		self::assertNotNull($returnTypeHint);
		self::assertNotNull($returnAnnotation);

		self::assertSame(
			$equals,
			TypeHintHelper::typeHintEqualsAnnotation(
				$phpcsFile,
				$functionPointer,
				$returnTypeHint->getTypeHint(),
				AnnotationTypeHelper::print($returnAnnotation->getValue()->type)
			)
		);
	}

}
