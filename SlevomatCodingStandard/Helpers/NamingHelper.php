<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function trim;
use const T_CLASS;
use const T_CONST;
use const T_FUNCTION;
use const T_STRING;
use const T_TRAIT;
use const T_VARIABLE;

class NamingHelper
{

	private const CODE_TO_TYPE_NAME_MAP = [
		T_CONST => 'Constant',
		T_CLASS => 'Class',
		T_FUNCTION => 'Function',
		T_TRAIT => 'Trait',
	];

	public static function getTypeName(File $file, int $position): string
	{
		$token = $file->getTokens()[$position];
		$tokenCode = $token['code'];
		if (isset(self::CODE_TO_TYPE_NAME_MAP[$tokenCode])) {
			return self::CODE_TO_TYPE_NAME_MAP[$tokenCode];
		}

		if ($token['code'] === T_VARIABLE) {
			if (PropertyHelper::isProperty($file, $position)) {
				return 'Property';
			}

			return 'Variable';
		}

		// @codeCoverageIgnoreStart
		return '';
		// @codeCoverageIgnoreEnd
	}

	public static function getElementName(File $file, int $position): string
	{
		$name = $file->getTokens()[$position]['content'];

		if (StringHelper::startsWith($name, '$')) {
			return trim($name, '$');
		}

		$namePosition = $file->findNext(T_STRING, $position, $position + 3);

		return $file->getTokens()[$namePosition]['content'];
	}

}
