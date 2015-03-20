<?php

namespace Pages\Controller\Frontend;

use Core\Db\EntityNotFoundException;
use Core\Controller;

abstract class PagesController extends Controller
{
    // get list of pages
    public function actionIndex()
    {

        $currentPage  = $this->app->request->get('p', 1);
        $itemsPerPage = $this->app->config->get('items_per_page')['frontend'];

        list($pages, $pagesNumber) = $this->getSearchResult(null, 'created_at', 'desc', $currentPage, $itemsPerPage);

        return $this->render('index', array(
            'pages'       => $pages,
            'currentPage' => $currentPage,
            'pagesNumber' => $pagesNumber,
            'token'       => $this->app['form.csrf_provider']->generateCsrfToken('ajax-protection')
        ));
    }

    public function actionPage($slug)
    {
        $page = $this->loadPageOr404($slug);

        return $this->render('page', [
            'page' => $page
        ]);
    }

    abstract public function getEntityClass();

    protected function error404()
    {
        throw new EntityNotFoundException($this->app->trans('Страница не найдена'));
    }

    protected function loadPageOr404($slug)
    {
        $entityClass = $this->getEntityClass();
        $page        = $entityClass::manager()->findActiveBySlug($slug);
        if (!$page) {
            return $this->error404();
        }

        return $page;
    }

    public function getSearchResult($searchQuery, $sortBy, $sortOrder, $currentPage, $itemsPerPage)
    {
        $entity = $this->getEntityClass();

        return $entity::manager()->search($searchQuery, $sortBy, $sortOrder, $currentPage, $itemsPerPage, true);
    }
}
