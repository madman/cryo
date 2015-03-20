<?php
namespace Pages\Controller\Frontend;

use Core\Db\EntityNotFoundException;

class NewsController extends PagesController
{
    public function getEntityClass()
    {
        return '\\Pages\\Entity\\News';
    }

    public function getSearchResult($searchQuery, $sortBy, $sortOrder, $currentPage, $itemsPerPage)
    {
        $entity = $this->getEntityClass();

        return $entity ::manager()->search(null, 'show_at', 'desc', $currentPage, $itemsPerPage, true, true);
    }

    protected function loadPageOr404($slug)
    {
        $entityClass = $this->getEntityClass();
        $page        = $entityClass::manager()->findPublishedBySlug($slug);
        if (!$page) {
            return $this->error404();
        }

        return $page;
    }
}