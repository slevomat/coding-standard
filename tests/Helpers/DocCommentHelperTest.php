<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class DocCommentHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	/** @var \PHP_CodeSniffer\Files\File */
	private $testedCodeSnifferFile;

	public function testClassHasDocComment()
	{
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocCommentAndDescription')));
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocComment')));
	}

	public function testClassHasNoDocComment()
	{
		$this->assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutDocComment')));
	}

	public function testClassHasDocCommentDescription()
	{
		$this->assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocCommentAndDescription')));
	}

	public function testClassHasNoDocCommentDescription()
	{
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocComment')));
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutDocComment')));
	}

	public function testConstantHasDocComment()
	{
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT_AND_DESCRIPTION')));
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT')));
	}

	public function testConstantHasNoDocComment()
	{
		$this->assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_DOC_COMMENT')));
	}

	public function testConstantHasDocCommentDescription()
	{
		$this->assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT_AND_DESCRIPTION')));
	}

	public function testConstantHasNoDocCommentDescription()
	{
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT')));
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_DOC_COMMENT')));
	}

	public function testPropertyHasDocComment()
	{
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
	}

	public function testPropertyHasNoDocComment()
	{
		$this->assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testPropertyHasDocCommentDescription()
	{
		$this->assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
	}

	public function testPropertyHasNoDocCommentDescription()
	{
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testFunctionHasDocComment()
	{
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
	}

	public function testFunctionHasNoDocComment()
	{
		$this->assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testFunctionHasDocCommentDescription()
	{
		$this->assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
	}

	public function testFunctionHasNoDocCommentDescription()
	{
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	private function getTestedCodeSnifferFile(): \PHP_CodeSniffer\Files\File
	{
		if ($this->testedCodeSnifferFile === null) {
			$this->testedCodeSnifferFile = $this->getCodeSnifferFile(
				__DIR__ . '/data/docComment.php'
			);
		}
		return $this->testedCodeSnifferFile;
	}

}
