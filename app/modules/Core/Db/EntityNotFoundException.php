<?php

namespace Core\Db;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class EntityNotFoundException - special exception needs to be thrown if we want to show we didn't find entity
 * @package Core\Db
 */
class EntityNotFoundException extends NotFoundHttpException
{

}