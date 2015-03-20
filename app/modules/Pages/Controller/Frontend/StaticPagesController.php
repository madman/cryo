<?php
namespace Pages\Controller\Frontend;

class StaticPagesController extends PagesController
{
    public function getEntityClass()
    {
        return '\\Pages\\Entity\\StaticPage';
    }
}