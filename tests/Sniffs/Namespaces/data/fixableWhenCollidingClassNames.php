<?php declare(strict_types=1);

namespace App\Controller\Common;

abstract class AbstractUserEditController
{

	/**
	 * @var \App\Process\UserClient\Edit|\App\Process\UserSuperAdmin\Edit
	 */
	protected $edit;

}
