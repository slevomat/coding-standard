<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use InvalidArgumentException;
use function count;
use const T_ATTRIBUTE;

class AttributeHelperTest extends TestCase
{

	public function testGetAttributesInsideAttributeTags(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithJoinedAttributes.php');
		/** @var int $firstAttributePointer */
		$firstAttributePointer = $phpcsFile->findNext(T_ATTRIBUTE, 0);

		$attributesPointers = AttributeHelper::getAttributesPointersInsideAttributeTags($phpcsFile, $firstAttributePointer);

		$expected = [
			4 => 'Attribute1',
			7 => '\\',
			17 => 'Attribute3',
			39 => '\\',
			44 => 'Attribute5',
		];

		self::assertCount(count($expected), $attributesPointers);

		$tokens = $phpcsFile->getTokens();

		foreach ($attributesPointers as $attributePointer) {
			self::assertArrayHasKey($attributePointer, $expected);
			self::assertSame($expected[$attributePointer], $tokens[$attributePointer]['content']);
		}
	}

	public function testIsValidAttributeAttributeAtEndOfFile(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/attributeAtEndOfFileTreatedAsCommentPriorPHP80.php');
		/** @var int $firstAttributePointer */
		$firstAttributePointer = $phpcsFile->findNext(T_ATTRIBUTE, 0);
		self::assertFalse(AttributeHelper::isValidAttribute($phpcsFile, $firstAttributePointer));
	}

	public function testIsValidAttributeAttributeAppliedIncorrectly(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/attributeAppliedIncorrectly.php');
		/** @var int $firstAttributePointer */
		$firstAttributePointer = $phpcsFile->findNext(T_ATTRIBUTE, 0);
		self::assertFalse(AttributeHelper::isValidAttribute($phpcsFile, $firstAttributePointer));
	}

	public function testGetAttributesInsideAttributeTagsException(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithJoinedAttributes.php');
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Token 0 must be attribute, T_OPEN_TAG given');
		AttributeHelper::getAttributesPointersInsideAttributeTags($phpcsFile, 0);
	}

}
