<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use function in_array;
use function sprintf;

/**
 * @internal
 */
class TemplateAnnotation extends Annotation
{

	/** @var TemplateTagValueNode|null */
	private $contentNode;

	public function __construct(string $name, int $startPointer, int $endPointer, ?string $content, ?TemplateTagValueNode $contentNode)
	{
		if (!in_array(
			$name,
			['@template', '@psalm-template', '@phpstan-template', '@template-covariant', '@psalm-template-covariant', '@phpstan-template-covariant'],
			true
		)) {
			throw new InvalidArgumentException(sprintf('Unsupported annotation %s.', $name));
		}

		parent::__construct($name, $startPointer, $endPointer, $content);

		$this->contentNode = $contentNode;
	}

	public function isInvalid(): bool
	{
		return $this->contentNode === null;
	}

	public function getContentNode(): TemplateTagValueNode
	{
		$this->errorWhenInvalid();

		return $this->contentNode;
	}

	public function hasDescription(): bool
	{
		return $this->getDescription() !== null;
	}

	public function getDescription(): ?string
	{
		$this->errorWhenInvalid();

		return $this->contentNode->description !== '' ? $this->contentNode->description : null;
	}

	public function getTemplateName(): string
	{
		$this->errorWhenInvalid();

		return $this->contentNode->name;
	}

	public function getBound(): ?TypeNode
	{
		$this->errorWhenInvalid();

		return $this->contentNode->bound;
	}

	public function getDefault(): ?TypeNode
	{
		$this->errorWhenInvalid();

		return $this->contentNode->default;
	}

	public function print(): string
	{
		return sprintf('%s %s', $this->name, AnnotationHelper::getPhpDocPrinter()->print($this->contentNode));
	}

}
