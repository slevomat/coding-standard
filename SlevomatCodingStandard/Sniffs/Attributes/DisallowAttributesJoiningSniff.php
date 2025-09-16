<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AttributeHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use function count;
use function sprintf;
use const T_ATTRIBUTE;

class DisallowAttributesJoiningSniff implements Sniff
{

	public const CODE_DISALLOWED_ATTRIBUTES_JOINING = 'DisallowedAttributesJoining';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_ATTRIBUTE];
	}

	public function process(File $phpcsFile, int $attributeOpenerPointer): void
	{
		if (!AttributeHelper::isValidAttribute($phpcsFile, $attributeOpenerPointer)) {
			return;
		}

		$attributes = AttributeHelper::getAttributes($phpcsFile, $attributeOpenerPointer);
		$attributeCount = count($attributes);

		if ($attributeCount === 1) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('%d attributes are joined.', $attributeCount),
			$attributeOpenerPointer,
			self::CODE_DISALLOWED_ATTRIBUTES_JOINING,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		for ($i = 1; $i < count($attributes); $i++) {
			$previousAttribute = $attributes[$i - 1];
			$attribute = $attributes[$i];

			FixerHelper::add($phpcsFile, $previousAttribute->getEndPointer(), ']');

			for ($j = $previousAttribute->getEndPointer() + 1; $j < $attribute->getStartPointer(); $j++) {
				if ($phpcsFile->fixer->getTokenContent($j) === ',') {
					FixerHelper::replace($phpcsFile, $j, '');
				}
			}

			FixerHelper::addBefore($phpcsFile, $attribute->getStartPointer(), '#[');
		}

		$phpcsFile->fixer->endChangeset();
	}

}
