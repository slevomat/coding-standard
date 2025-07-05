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
			['Attribute1', '\\Attribute1', null],
			['\\FQN\\Attribute2', '\\FQN\\Attribute2', "('var')"],
			['Attribute3', '\\Attribute3', "(option: PDO::class, option2: true, option3: 'False')"],
			['\\Attribute4', '\\Attribute4', '()'],
			['Attribute5', '\\Attribute5', null],
			['Attribute6', '\\FQN\\Attribute6', null],
		];

		self::assertCount(count($expected), $attributes);

		foreach ($attributes as $attributeNo => $attribute) {
			self::assertSame(11, $attribute->getAttributePointer());
			self::assertSame($expected[$attributeNo][0], $attribute->getName());
			self::assertSame($expected[$attributeNo][1], $attribute->getFullyQualifiedName());
			self::assertSame($expected[$attributeNo][2], $attribute->getContent());
		}
	}

	public function testIsValidAttributeAttributeAtEndOfFile(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/invalidAttribute.php');
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
