<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AttributeHelper;
use function count;
use function sprintf;
use const T_ATTRIBUTE;
use const T_COMMA;

class DisallowAttributeJoiningSniff implements Sniff
{

	public const CODE_DISALLOWED_ATTRIBUTE_JOINING = 'DisallowedAttributeJoining';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_ATTRIBUTE];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $attributeOpenPointer
	 */
	public function process(File $phpcsFile, $attributeOpenPointer): void
	{
		if (!AttributeHelper::isValidAttribute($phpcsFile, $attributeOpenPointer)) {
			return;
		}

		$attributes = AttributeHelper::getAttributes($phpcsFile, $attributeOpenPointer);
		$attributeCount = count($attributes);

		if ($attributeCount === 1) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('%d attributes are joined.', $attributeCount),
			$attributeOpenPointer,
			self::CODE_DISALLOWED_ATTRIBUTE_JOINING
		);

		if (!$fix) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$phpcsFile->fixer->beginChangeset();

		for ($i = 1; $i < count($attributes); $i++) {
			$previousAttribute = $attributes[$i - 1];
			$attribute = $attributes[$i];

			$phpcsFile->fixer->addContent($previousAttribute->getEndPointer(), ']');

			for ($j = $previousAttribute->getEndPointer() + 1; $j < $attribute->getStartPointer(); $j++) {
				if ($tokens[$j]['code'] === T_COMMA) {
					$phpcsFile->fixer->replaceToken($j, '');
				}
			}

			$phpcsFile->fixer->addContentBefore($attribute->getStartPointer(), '#[');
		}

		$phpcsFile->fixer->endChangeset();
	}

}
