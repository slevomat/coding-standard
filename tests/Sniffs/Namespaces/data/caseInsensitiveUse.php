<?php

use Foo\Bar;
use Foo\Boo;
use Exception;
use Uuid;
use Route;
use Ignore;
use PropertyAnnotation;
use PropertyAnnotationDescription;
use PropertyReadAnnotation;
use PropertyReadAnnotationDescription;
use PropertyWriteAnnotation;
use PropertyWriteAnnotationDescription;
use VarAnnotation;
use VarAnnotationDescription;
use ParamAnnotation;
use ParamAnnotationDescription;
use ReturnAnnotation;
use ReturnAnnotationDescription;
use ThrowsAnnotation;
use ThrowsAnnotationDescription;
use MethodAnnotation;
use MethodAnnotationDescription;
use Param;
use Throws;
use Property;
use Method;

new bar();

new BAR();

/** @var boo */
/** @BOO */
/**
 * @ORM\OneToMany(targetEntity=boo::class, mappedBy="boo")
 */

/**
 * exception (at the beginning of description)
 */

/**
 * Whatever exception (in the middle of description)
 */

/**
 * (at the end of description) exception
 */

/**
 * @param \Exception exception in the param annotation
 */

/**
 * @return \Exception exception in the param annotation
 */

new Exception();
new Uuid();
new Route();

/**
 * @Serializer\Type("uuid")
 */

/**
 * @Route("/uuid/example")
 */

/**
 * @Route("/widget/list", name="widget_list")
 * @Route("/widget/view/{uuid}", name="widget_view")
 */


/**
 * @ignore(foo=uuid::class)
 */

/**
 * @property PropertyAnnotation $property propertyAnnotationDescription
 * @property-read PropertyReadAnnotation $propertyRead propertyReadAnnotationDescription
 * @property-write PropertyWriteAnnotation $propertyWrite propertyWriteAnnotationDescription
 * @method MethodAnnotation method(MethodParameter1 $m, MethodParameter2 $m2) methodAnnotationDescription
 */
class Foo
{

	/** @var VarAnnotation varAnnotationDescription */
	private $varAnnotation;

	/**
	 * @param ParamAnnotation $paramAnnotation paramAnnotationDescription
	 * @return ReturnAnnotation returnAnnotationDescription
	 * @throws ThrowsAnnotation throwsAnnotationDescription
	 */
	public function method($paramAnnotation)
	{
		return null;
	}

}

/**
 * @group ignore
 */
class Fooo
{

}
