<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class DocCommentHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	/** @var \PHP_CodeSniffer\Files\File */
	private $testedCodeSnifferFile;

	public function testClassHasDocComment(): void
	{
		self::assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocCommentAndDescription')));
		self::assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocComment')));
	}

	public function testClassGetDocComment(): void
	{
		self::assertSame("* Class WithDocComment\n *\n * @see https://www.slevomat.cz", DocCommentHelper::getDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocCommentAndDescription')));
		self::assertNull(DocCommentHelper::getDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutDocComment')));
	}

	public function testClassHasNoDocComment(): void
	{
		self::assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutDocComment')));
	}

	public function testClassHasEmptyDocComment(): void
	{
		self::assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'EmptyDocComment')));
		self::assertNull(DocCommentHelper::getDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'EmptyDocComment')));
	}

	public function testClassHasDocCommentDescription(): void
	{
		self::assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocCommentAndDescription')));
	}

	public function testClassHasNoDocCommentDescription(): void
	{
		self::assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithDocComment')));
		self::assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'WithoutDocComment')));
	}

	public function testConstantHasDocComment(): void
	{
		self::assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT_AND_DESCRIPTION')));
		self::assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT')));
	}

	public function testConstantHasNoDocComment(): void
	{
		self::assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_DOC_COMMENT')));
	}

	public function testConstantHasDocCommentDescription(): void
	{
		self::assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT_AND_DESCRIPTION')));
	}

	public function testConstantHasNoDocCommentDescription(): void
	{
		self::assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT')));
		self::assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITHOUT_DOC_COMMENT')));
	}

	public function testPropertyHasDocComment(): void
	{
		self::assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
		self::assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
	}

	public function testPropertyHasNoDocComment(): void
	{
		self::assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testPropertyHasDocCommentDescription(): void
	{
		self::assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
	}

	public function testPropertyHasNoDocCommentDescription(): void
	{
		self::assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
		self::assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testFunctionHasDocComment(): void
	{
		self::assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
		self::assertTrue(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
	}

	public function testFunctionHasNoDocComment(): void
	{
		self::assertFalse(DocCommentHelper::hasDocComment($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testFunctionHasDocCommentDescription(): void
	{
		self::assertTrue(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')));
	}

	public function testFunctionHasNoDocCommentDescription(): void
	{
		self::assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocComment')));
		self::assertFalse(DocCommentHelper::hasDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withoutDocComment')));
	}

	public function testConstantGetDocCommentDescription(): void
	{
		self::assertEquals(
			['Constant WITH_DOC_COMMENT_AND_DESCRIPTION'],
			$this->stringifyComments(DocCommentHelper::getDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'WITH_DOC_COMMENT_AND_DESCRIPTION')))
		);
	}

	public function testPropertyGetDocCommentDescription(): void
	{
		self::assertSame(
			['Property with doc comment and description'],
			$this->stringifyComments(DocCommentHelper::getDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')))
		);
	}

	public function testFunctionGetDocCommentDescription(): void
	{
		self::assertSame(
			['Function with doc comment and description', 'And is multi-line'],
			$this->stringifyComments(DocCommentHelper::getDocCommentDescription($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), 'withDocCommentAndDescription')))
		);
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

	/**
	 * @param \SlevomatCodingStandard\Helpers\Comment[] $comments
	 * @return string[]
	 */
	private function stringifyComments(array $comments): array
	{
		return array_map(function (Comment $comment): string {
			return $comment->getContent();
		}, $comments);
	}

}
