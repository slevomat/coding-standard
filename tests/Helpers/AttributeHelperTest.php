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

		$attributes = AttributeHelper::getAttributes($phpcsFile, $firstAttributePointer);

		$expected = [
			4 => ['Attribute1', null],
			7 => ['\\FQN\\Attribute2', "('var')"],
			17 => ['Attribute3', "(option: PDO::class, option2: true, option3: 'False')"],
			39 => ['\\Attribute4', '()'],
			44 => ['Attribute5', null],
		];

		self::assertCount(count($expected), $attributes);

		foreach ($attributes as $attribute) {
			self::assertSame(3, $attribute->getAttributePointer());
			self::assertArrayHasKey($attribute->getStartPointer(), $expected);
			self::assertSame($expected[$attribute->getStartPointer()][0], $attribute->getName());
			self::assertSame($expected[$attribute->getStartPointer()][1], $attribute->getContent());
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
		AttributeHelper::getAttributes($phpcsFile, 0);
	}

}
