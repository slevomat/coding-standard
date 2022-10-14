<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use InvalidArgumentException;
use const T_ATTRIBUTE;

class AttributeHelperTest extends TestCase
{

	public function testGetAttributesInsideAttributeTags(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/classWithJoinedAttributes.php');
		/** @var int $firstAttributePointer */
		$firstAttributePointer = $phpcsFile->findNext(T_ATTRIBUTE, 0);
		self::assertSame(
			[4, 7, 14, 36, 40],
			AttributeHelper::getAttributesPointersInsideAttributeTags($phpcsFile, $firstAttributePointer)
		);
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
