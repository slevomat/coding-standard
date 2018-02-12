<?php

use Foo\Bar;
use Foo\Boo;
use Exception;
use Uuid;
use Route;
use Ignore;

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
