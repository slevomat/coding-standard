<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\PropertyAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation;

class AnnotationHelperTest extends TestCase
{

	/** @var File */
	private $testedCodeSnifferFile;

	public function testClassWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithAnnotation'),
			'@see'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(GenericAnnotation::class, $annotations[0]);
		self::assertSame('@see', $annotations[0]->getName());
		self::assertSame(4, $this->getLineByPointer($annotations[0]->getStartPointer()));
		self::assertSame('https://www.slevomat.cz', $annotations[0]->getContent());
	}

	public function testClassWithoutAnnotation(): void
	{
		self::assertCount(
			0,
			AnnotationHelper::getAnnotationsByName(
				$this->getTestedCodeSnifferFile(),
				$this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutAnnotation'),
				'@see'
			)
		);
	}

	public function testConstantWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_ANNOTATION'),
			'@var'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(VariableAnnotation::class, $annotations[0]);
		self::assertSame('@var', $annotations[0]->getName());
		self::assertSame(10, $this->getLineByPointer($annotations[0]->getStartPointer()));
		self::assertSame('bool', $annotations[0]->getContent());
		self::assertFalse($annotations[0]->hasDescription());
		self::assertNull($annotations[0]->getDescription());
		self::assertSame('bool', (string) $annotations[0]->getType());
	}

	public function testConstantWithoutAnnotation(): void
	{
		self::assertCount(
			0,
			AnnotationHelper::getAnnotationsByName(
				$this->getTestedCodeSnifferFile(),
				$this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_ANNOTATION'),
				'@var'
			)
		);
	}

	public function testPropertyWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withAnnotation'),
			'@var'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(VariableAnnotation::class, $annotations[0]);
		self::assertSame('@var', $annotations[0]->getName());
		self::assertSame(17, $this->getLineByPointer($annotations[0]->getStartPointer()));
		self::assertSame('null|int|float', $annotations[0]->getContent());
		self::assertFalse($annotations[0]->hasDescription());
		self::assertNull($annotations[0]->getDescription());
		self::assertSame('(null | int | float)', (string) $annotations[0]->getType());
	}

	public function testPropertyWithoutAnnotation(): void
	{
		self::assertCount(
			0,
			AnnotationHelper::getAnnotationsByName(
				$this->getTestedCodeSnifferFile(),
				$this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutAnnotation'),
				'@var'
			)
		);
	}

	public function testReturnAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withReturnAnnotation'),
			'@return'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(ReturnAnnotation::class, $annotations[0]);
		self::assertSame('@return', $annotations[0]->getName());
		self::assertSame(81, $this->getLineByPointer($annotations[0]->getStartPointer()));
		self::assertSame('string|null', $annotations[0]->getContent());
		self::assertFalse($annotations[0]->hasDescription());
		self::assertNull($annotations[0]->getDescription());
		self::assertSame('(string | null)', (string) $annotations[0]->getType());
	}

	public function testFunctionWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withAnnotation'),
			'@param'
		);

		self::assertCount(2, $annotations);

		self::assertInstanceOf(ParameterAnnotation::class, $annotations[0]);
		self::assertSame('@param', $annotations[0]->getName());
		self::assertSame(29, $this->getLineByPointer($annotations[0]->getStartPointer()));
		self::assertSame('string $a', $annotations[0]->getContent());
		self::assertFalse($annotations[0]->hasDescription());
		self::assertNull($annotations[0]->getDescription());
		self::assertSame('$a', $annotations[0]->getParameterName());
		self::assertSame('string', (string) $annotations[0]->getType());

		self::assertInstanceOf(ParameterAnnotation::class, $annotations[1]);
		self::assertSame('@param', $annotations[1]->getName());
		self::assertSame(30, $this->getLineByPointer($annotations[1]->getStartPointer()));
		self::assertSame('int|null $b', $annotations[1]->getContent());
		self::assertFalse($annotations[1]->hasDescription());
		self::assertNull($annotations[1]->getDescription());
		self::assertSame('$b', $annotations[1]->getParameterName());
		self::assertSame('(int | null)', (string) $annotations[1]->getType());
	}

	public function testFunctionWithParametrizedAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotation'),
			'@Route'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(GenericAnnotation::class, $annotations[0]);
		self::assertSame('"/", name="homepage"', $annotations[0]->getParameters());
		self::assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithParametrizedAnnotationContainingParenthesis(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotationContainingParenthesis'),
			'@Security'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(GenericAnnotation::class, $annotations[0]);
		self::assertSame('"is_granted(\'ROLE_ADMIN\')"', $annotations[0]->getParameters());
		self::assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithMultiLineParametrizedAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withMultiLineParametrizedAnnotation'),
			'@Route'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(GenericAnnotation::class, $annotations[0]);
		self::assertSame(
			"\"/configs/{config}/domains/{domain}/locales/{locale}/messages\", name=\"jms_translation_update_message\",\ndefaults = {\"id\" = null}, options = {\"i18n\" = false}, methods={\"PUT\"}",
			$annotations[0]->getParameters()
		);
		self::assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithParametrizedAnnotationWithoutParameters(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotationWithoutParameters'),
			'@Assert\Callback'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(GenericAnnotation::class, $annotations[0]);
		self::assertNull($annotations[0]->getParameters());
		self::assertNull($annotations[0]->getContent());
	}

	public function testInlineDocCommentWithParametrizedAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'inlineDocComment'),
			'@ORM\OneToMany'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(GenericAnnotation::class, $annotations[0]);
		self::assertSame('targetEntity=Bar::class, mappedBy="boo"', $annotations[0]->getParameters());
		self::assertNull($annotations[0]->getContent());
	}

	public function testWordPressAnnotations(): void
	{
		$annotations = AnnotationHelper::getAnnotations(
			$this->getTestedCodeSnifferFile(),
			$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'wordPress')
		);

		self::assertCount(1, $annotations);
		self::assertCount(1, $annotations['@param']);

		$annotation = $annotations['@param'][0];

		self::assertInstanceOf(ParameterAnnotation::class, $annotation);
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getType());
		self::assertSame('$parameters', $annotation->getParameterName());
		self::assertSame(
			'{ Optional. Parameters for filtering the list of user assignments. Default empty array. @type bool $is_active                Pass `true` to only return active user assignments and `false` to return  inactive user assignments. @type DateTime|string $updated_since Only return user assignments that have been updated since the given date and time. }',
			$annotation->getDescription()
		);
	}

	public function testFunctionWithoutAnnotation(): void
	{
		self::assertCount(
			0,
			AnnotationHelper::getAnnotationsByName(
				$this->getTestedCodeSnifferFile(),
				$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withoutAnnotation'),
				'@param'
			)
		);
	}

	public function testMultiLineIndentedAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotations(
			$this->getTestedCodeSnifferFile(),
			$this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'multiLineIndentedAnnotation')
		);

		self::assertCount(1, $annotations);
		self::assertArrayHasKey('@X', $annotations);

		$xAnnotations = $annotations['@X'];

		self::assertCount(1, $xAnnotations);

		self::assertInstanceOf(GenericAnnotation::class, $xAnnotations[0]);
		self::assertSame('@X', $xAnnotations[0]->getName());
		self::assertSame('Content', $xAnnotations[0]->getContent());
		self::assertSame(64, $this->getLineByPointer($xAnnotations[0]->getStartPointer()));
		self::assertSame(71, $this->getLineByPointer($xAnnotations[0]->getEndPointer()));
	}

	public function testAnnotationWithDash(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName(
			$this->getTestedCodeSnifferFile(),
			$this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'annotationWithDash'),
			'@property-read'
		);

		self::assertCount(1, $annotations);

		self::assertInstanceOf(PropertyAnnotation::class, $annotations[0]);
		self::assertSame('Foo $propertyRead Description', $annotations[0]->getContent());
		self::assertSame('$propertyRead', $annotations[0]->getPropertyName());
		self::assertTrue($annotations[0]->hasDescription());
		self::assertSame('Description', $annotations[0]->getDescription());
	}

	private function getTestedCodeSnifferFile(): File
	{
		if ($this->testedCodeSnifferFile === null) {
			$this->testedCodeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/annotation.php');
		}
		return $this->testedCodeSnifferFile;
	}

	private function getLineByPointer(int $pointer): int
	{
		return $this->getTestedCodeSnifferFile()->getTokens()[$pointer]['line'];
	}

}
