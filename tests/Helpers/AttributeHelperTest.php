<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function count;
use const T_ATTRIBUTE;
use const T_FUNCTION;

class AttributeHelperTest extends TestCase
{

	public function testHasAttribute(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/attributes.php');

		$functionsPointers = TokenHelper::findNextAll($phpcsFile, T_FUNCTION, 0);

		self::assertCount(2, $functionsPointers);

		foreach ($functionsPointers as $functionPointer) {
			self::assertTrue(
				AttributeHelper::hasAttribute($phpcsFile, $functionPointer, '\Attribute1'),
				FunctionHelper::getName($phpcsFile, $functionPointer),
			);
		}
	}

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

}
