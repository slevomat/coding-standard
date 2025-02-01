<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AttributeHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use UnexpectedValueException;
use function array_keys;
use function array_map;
use function asort;
use function count;
use function ltrim;
use function strnatcmp;
use function strpos;
use function substr;
use function trim;
use function uasort;
use const T_ATTRIBUTE;
use const T_ATTRIBUTE_END;

class AttributesOrderSniff implements Sniff
{

	public const CODE_INCORRECT_ORDER = 'IncorrectOrder';

	/** @var list<string> */
	public array $order = [];

	public bool $orderAlphabetically = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_ATTRIBUTE];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $attributeOpenerPointer
	 */
	public function process(File $phpcsFile, $attributeOpenerPointer): void
	{
		if (!AttributeHelper::isValidAttribute($phpcsFile, $attributeOpenerPointer)) {
			return;
		}

		if ($this->order === [] && !$this->orderAlphabetically) {
			throw new UnexpectedValueException('Neither manual or alphabetical order is set.');
		}

		if ($this->order !== [] && $this->orderAlphabetically) {
			throw new UnexpectedValueException('Only one order can be set.');
		}

		$this->order = $this->normalizeOrder($this->order);

		$tokens = $phpcsFile->getTokens();

		$pointerBefore = TokenHelper::findPreviousNonWhitespace($phpcsFile, $attributeOpenerPointer - 1);

		if ($tokens[$pointerBefore]['code'] === T_ATTRIBUTE_END) {
			return;
		}

		$attributesGroups = [AttributeHelper::getAttributes($phpcsFile, $attributeOpenerPointer)];

		$lastAttributeCloserPointer = $tokens[$attributeOpenerPointer]['attribute_closer'];

		do {
			$nextPointer = TokenHelper::findNextNonWhitespace($phpcsFile, $lastAttributeCloserPointer + 1);

			if ($tokens[$nextPointer]['code'] !== T_ATTRIBUTE) {
				break;
			}

			$attributesGroups[] = AttributeHelper::getAttributes($phpcsFile, $nextPointer);

			$lastAttributeCloserPointer = $tokens[$nextPointer]['attribute_closer'];

		} while (true);

		if ($this->orderAlphabetically) {
			$actualOrder = $attributesGroups;
			$expectedOrder = $actualOrder;

			uasort(
				$expectedOrder,
				static fn (array $attributesGroup1, array $attributesGroup2): int => strnatcmp(
					$attributesGroup1[0]->getName(),
					$attributesGroup2[0]->getName(),
				),
			);

		} else {
			$actualOrder = [];

			foreach ($attributesGroups as $attributesGroupNo => $attributesGroup) {
				$attributeName = $this->normalizeAttributeName($attributesGroup[0]->getName());

				foreach ($this->order as $orderPosition => $attributeNameOnPosition) {
					if (
						$attributeName === $attributeNameOnPosition
						|| (
							substr($attributeNameOnPosition, -1) === '\\'
							&& strpos($attributeName, $attributeNameOnPosition) === 0
						)
						|| (
							substr($attributeNameOnPosition, -1) === '*'
							&& strpos($attributeName, substr($attributeNameOnPosition, 0, -1)) === 0
						)
					) {
						$actualOrder[$attributesGroupNo] = $orderPosition;
						continue 2;
					}
				}

				// Unknown order - add to the end
				$actualOrder[$attributesGroupNo] = 999;
			}

			$expectedOrder = $actualOrder;
			asort($expectedOrder);
		}

		if ($expectedOrder === $actualOrder) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Incorrect order of attributes.', $attributeOpenerPointer, self::CODE_INCORRECT_ORDER);

		if (!$fix) {
			return;
		}

		$attributesGroupsContent = [];
		foreach ($attributesGroups as $attributesGroupNo => $attributesGroup) {
			$attributesGroupsContent[$attributesGroupNo] = TokenHelper::getContent(
				$phpcsFile,
				$attributesGroup[0]->getAttributePointer(),
				$tokens[$attributesGroup[0]->getAttributePointer()]['attribute_closer'],
			);
		}

		$areOnSameLine = $tokens[$attributeOpenerPointer]['line'] === $tokens[$lastAttributeCloserPointer]['line'];

		$attributesStartPointer = $attributeOpenerPointer;
		$attributesEndPointer = $lastAttributeCloserPointer;
		$indentation = IndentationHelper::getIndentation($phpcsFile, $attributeOpenerPointer);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetweenIncluding($phpcsFile, $attributesStartPointer, $attributesEndPointer);

		foreach (array_keys($expectedOrder) as $position => $attributesGroupNo) {
			if ($areOnSameLine) {
				if ($position !== 0) {
					$phpcsFile->fixer->addContent($attributesStartPointer, ' ');
				}

				$phpcsFile->fixer->addContent($attributesStartPointer, $attributesGroupsContent[$attributesGroupNo]);
			} else {
				if ($position !== 0) {
					$phpcsFile->fixer->addContent($attributesStartPointer, $indentation);
				}

				$phpcsFile->fixer->addContent($attributesStartPointer, $attributesGroupsContent[$attributesGroupNo]);

				if ($position !== count($attributesGroups) - 1) {
					$phpcsFile->fixer->addNewline($attributesStartPointer);
				}
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param list<string> $order
	 * @return list<string>
	 */
	private function normalizeOrder(array $order): array
	{
		return array_map(fn (string $item): string => $this->normalizeAttributeName(trim($item)), $order);
	}

	private function normalizeAttributeName(string $name): string
	{
		return ltrim($name, '\\');
	}

}
