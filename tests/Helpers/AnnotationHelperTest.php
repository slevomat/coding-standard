<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class AnnotationHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	/** @var \PHP_CodeSniffer\Files\File */
	private $testedCodeSnifferFile;

	public function testClassWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithAnnotation'), '@see');
		$this->assertCount(1, $annotations);
		$this->assertSame('@see', $annotations[0]->getName());
		$this->assertSame(4, $this->getLineByPointer($annotations[0]->getPointer()));
		$this->assertSame('https://www.slevomat.cz', $annotations[0]->getContent());
	}

	public function testClassWithoutAnnotation(): void
	{
		$this->assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutAnnotation'), '@see'));
	}

	public function testConstantWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_ANNOTATION'), '@var');
		$this->assertCount(1, $annotations);
		$this->assertSame('@var', $annotations[0]->getName());
		$this->assertSame(10, $this->getLineByPointer($annotations[0]->getPointer()));
		$this->assertSame('bool', $annotations[0]->getContent());
	}

	public function testConstantWithoutAnnotation(): void
	{
		$this->assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_ANNOTATION'), '@var'));
	}

	public function testPropertyWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withAnnotation'), '@var');
		$this->assertCount(1, $annotations);
		$this->assertSame('@var', $annotations[0]->getName());
		$this->assertSame(17, $this->getLineByPointer($annotations[0]->getPointer()));
		$this->assertSame('int', $annotations[0]->getContent());
	}

	public function testPropertyWithoutAnnotation(): void
	{
		$this->assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutAnnotation'), '@var'));
	}

	public function testFunctionWithAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withAnnotation'), '@param');
		$this->assertCount(2, $annotations);
		$this->assertSame('@param', $annotations[0]->getName());
		$this->assertSame(29, $this->getLineByPointer($annotations[0]->getPointer()));
		$this->assertSame('string $a', $annotations[0]->getContent());
		$this->assertSame('@param', $annotations[1]->getName());
		$this->assertSame(30, $this->getLineByPointer($annotations[1]->getPointer()));
		$this->assertSame('string $b', $annotations[1]->getContent());
	}

	public function testFunctionWithParametrizedAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotation'), '@Route');
		$this->assertCount(1, $annotations);
		$this->assertSame('"/", name="homepage"', $annotations[0]->getParameters());
		$this->assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithMultilineParametrizedAnnotation(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withMultilineParametrizedAnnotation'), '@Route');
		$this->assertCount(1, $annotations);
		$this->assertSame("\"/configs/{config}/domains/{domain}/locales/{locale}/messages\", name=\"jms_translation_update_message\",\ndefaults = {\"id\" = null}, options = {\"i18n\" = false}, methods={\"PUT\"}", $annotations[0]->getParameters());
		$this->assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithParametrizedAnnotationWithoutParameters(): void
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotationWithoutParameters'), '@Assert\Callback');
		$this->assertCount(1, $annotations);
		$this->assertNull($annotations[0]->getParameters());
		$this->assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithoutAnnotation(): void
	{
		$this->assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withoutAnnotation'), '@param'));
	}

	private function getTestedCodeSnifferFile(): \PHP_CodeSniffer\Files\File
	{
		if ($this->testedCodeSnifferFile === null) {
			$this->testedCodeSnifferFile = $this->getCodeSnifferFile(
				__DIR__ . '/data/annotation.php'
			);
		}
		return $this->testedCodeSnifferFile;
	}

	private function getLineByPointer(int $pointer): int
	{
		return $this->getTestedCodeSnifferFile()->getTokens()[$pointer]['line'];
	}

}
