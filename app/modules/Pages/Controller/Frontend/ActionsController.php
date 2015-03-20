<?php
namespace Pages\Controller\Frontend;

class ActionsController extends PagesController
{
    public function getEntityClass()
    {
        return '\\Pages\\Entity\\Action';
    }
}