<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class DocCommentHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	/** @var \PHP_CodeSniffer\Files\File */
	private $testedCodeSnifferFile;

	public function testClassHasDocComment(): void
	{
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocCommentAndDescription')));
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocComment')));
	}

	public function testClassGetDocComment(): void
	{
		$this->assertSame("* Class WithDocComment\n *\n * @see https://www.slevomat.cz", DocCommentHelper::getDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocCommentAndDescription')));
		$this->assertNull(DocCommentHelper::getDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutDocComment')));
	}

	public function testClassHasNoDocComment(): void
	{
		$this->assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutDocComment')));
	}

	public function testClassHasDocCommentDescription(): void
	{
		$this->assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocCommentAndDescription')));
	}

	public function testClassHasNoDocCommentDescription(): void
	{
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocComment')));
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutDocComment')));
	}

	public function testConstantHasDocComment(): void
	{
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT_AND_DESCRIPTION')));
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT')));
	}

	public function testConstantHasNoDocComment(): void
	{
		$this->assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_DOC_COMMENT')));
	}

	public function testConstantHasDocCommentDescription(): void
	{
		$this->assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT_AND_DESCRIPTION')));
	}

	public function testConstantHasNoDocCommentDescription(): void
	{
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT')));
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_DOC_COMMENT')));
	}

	public function testPropertyHasDocComment(): void
	{
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
	}

	public function testPropertyHasNoDocComment(): void
	{
		$this->assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testPropertyHasDocCommentDescription(): void
	{
		$this->assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
	}

	public function testPropertyHasNoDocCommentDescription(): void
	{
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
		$this->assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testFunctionHasDocComment(): void
	{
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
		$this->assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
	}

	public function testFunctionHasNoDocComment(): void
	{
		$this->assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testFunctionHasDocCommentDescription(): void
	{
		$this->assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
	}

	public function testFunctionHasNoDocCommentDescription(): void
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
