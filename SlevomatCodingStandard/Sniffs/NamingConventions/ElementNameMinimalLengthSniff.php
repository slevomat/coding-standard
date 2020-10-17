<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamingHelper;
use function in_array;
use function mb_strlen;
use function sprintf;
use const T_CLASS;
use const T_CONST;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_TRAIT;
use const T_VARIABLE;

final class ElementNameMinimalLengthSniff implements Sniff
{

	public const CODE_ELEMENT_NAME_MINIMAL_LENGTH = 'ElementNameMinimalLength';
	private const ERROR_MESSAGE = '%s name "%s" is only %d chars long. Must be at least %d.';

	/** @var int */
	public $minLength = 3;

	/** @var string[] */
	public $allowedShortNames = ['i', 'id', 'to', 'up'];

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [T_CLASS, T_TRAIT, T_INTERFACE, T_CONST, T_FUNCTION, T_VARIABLE];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $file
	 * @param int $position
	 */
	public function process(File $file, $position): void
	{
		$elementName = NamingHelper::getElementName($file, $position);
		$elementNameLength = mb_strlen($elementName);

		if ($this->shouldBeSkipped($elementNameLength, $elementName)) {
			return;
		}

		$typeName = NamingHelper::getTypeName($file, $position);
		$message = sprintf(self::ERROR_MESSAGE, $typeName, $elementName, $elementNameLength, $this->minLength);
		$file->addError($message, $position, self::CODE_ELEMENT_NAME_MINIMAL_LENGTH);
	}

	private function shouldBeSkipped(int $elementNameLength, string $elementName): bool
	{
		if ($elementNameLength >= $this->minLength) {
			return true;
		}

		return $this->isShortNameAllowed($elementName);
	}

	private function isShortNameAllowed(string $variableName): bool
	{
		return in_array($variableName, $this->allowedShortNames, true);
	}

}
