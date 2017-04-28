<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class AnnotationHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	/** @var \PHP_CodeSniffer\Files\File */
	private $testedCodeSnifferFile;

	public function testClassWithAnnotation()
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithAnnotation'), '@see');
		$this->assertCount(1, $annotations);
		$this->assertSame('https://www.slevomat.cz', $annotations[0]->getContent());
	}

	public function testClassWithoutAnnotation()
	{
		$this->assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutAnnotation'), '@see'));
	}

	public function testConstantWithAnnotation()
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_ANNOTATION'), '@var');
		$this->assertCount(1, $annotations);
		$this->assertSame('bool', $annotations[0]->getContent());
	}

	public function testConstantWithoutAnnotation()
	{
		$this->assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_ANNOTATION'), '@var'));
	}

	public function testPropertyWithAnnotation()
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withAnnotation'), '@var');
		$this->assertCount(1, $annotations);
		$this->assertSame('int', $annotations[0]->getContent());
	}

	public function testPropertyWithoutAnnotation()
	{
		$this->assertCount(0, AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutAnnotation'), '@var'));
	}

	public function testFunctionWithAnnotation()
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withAnnotation'), '@param');
		$this->assertCount(2, $annotations);
		$this->assertSame('string $a', $annotations[0]->getContent());
		$this->assertSame('string $b', $annotations[1]->getContent());
	}

	public function testFunctionWithParametrizedAnnotation()
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotation'), '@Route');
		$this->assertCount(1, $annotations);
		$this->assertSame('"/", name="homepage"', $annotations[0]->getParameters());
		$this->assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithParametrizedAnnotationWithoutParameters()
	{
		$annotations = AnnotationHelper::getAnnotationsByName($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withParametrizedAnnotationWithoutParameters'), '@Assert\Callback');
		$this->assertCount(1, $annotations);
		$this->assertNull($annotations[0]->getParameters());
		$this->assertNull($annotations[0]->getContent());
	}

	public function testFunctionWithoutAnnotation()
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

}
