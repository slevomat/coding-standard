<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AttributeHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function sprintf;
use const T_ATTRIBUTE;
use const T_CLOSE_PARENTHESIS;
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

		$attributes = AttributeHelper::getAttributesPointersInsideAttributeTags($phpcsFile, $attributeOpenPointer);
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

		$phpcsFile->fixer->beginChangeset();

		for ($i = 1; $i < $attributeCount; $i++) {
			$attributeNamePointer = $attributes[$i];
			/** @var int $separatingCommaPointer */
			$separatingCommaPointer = TokenHelper::findPrevious($phpcsFile, T_COMMA, $attributeNamePointer);
			/** @var int $previousAttributeNameEndPointer */
			$previousAttributeNameEndPointer = TokenHelper::findPrevious(
				$phpcsFile,
				[T_COMMA, T_CLOSE_PARENTHESIS],
				$separatingCommaPointer
			);
			$phpcsFile->fixer->addContentBefore($attributeNamePointer, '#[');
			$phpcsFile->fixer->replaceToken($separatingCommaPointer, '');
			$phpcsFile->fixer->addContent($previousAttributeNameEndPointer, ']');
		}

		$phpcsFile->fixer->endChangeset();
	}

}
