<?php

namespace Pages\Entity;

use Core\Db\Manager;

abstract class PageManager extends Manager
{
    protected $collection = 'pages';
    protected $query;

    public function findBySlug($slug)
    {
        $entityClass = $this->getEntityClass();
        $entity      = new $entityClass;

        return $this->findOne(['slug' => $slug, 'type' => $entity->getType()]);
    }

    public function findActiveBySlug($slug)
    {
        $entityClass = $this->getEntityClass();
        $entity      = new $entityClass;

        return $this->findOne(['slug' => $slug, 'type' => $entity->getType(), 'is_active' => true]);
    }

    /**
     * @param $searchQuery
     * @param $sortBy
     * @param $sortOrder
     * @param $currentPage
     * @param null $itemsPerPage
     * @param bool $onlyActive
     * @param bool $onlyActual
     * @return array
     * @throws \InvalidArgumentException
     */
    public function search($searchQuery, $sortBy, $sortOrder, $currentPage, $itemsPerPage = null, $onlyActive = false, $onlyActual = false)
    {
        if ($sortBy && !in_array($sortBy, ['name', 'created_at', 'updated_at', 'show_at'])) {
            throw new \InvalidArgumentException("Incorrect 'sort' value ");
        }

        if (!$sortOrder) {
            $sortOrder = 'asc';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            throw new \InvalidArgumentException("Incorrect 'order' value ");
        }

        $sortOrder = ($sortOrder === 'asc') ? 1 : -1;

        $query = $this->getQuery($searchQuery, $onlyActive, $onlyActual);

        $itemsPerPage = ($itemsPerPage) ? : $this->app->config->get('items_per_page')['backend'];

        $searchResult = $this->find($query)
            ->skip(($currentPage - 1) * $itemsPerPage)
            ->limit($itemsPerPage);
        if ($sortBy) {
            $searchResult->sort([$sortBy => $sortOrder]);
        }

        return [$searchResult, ceil($this->find($query)->count() / $itemsPerPage)];
    }

    /**
     * @param $searchQuery
     * @param $onlyActive - status is active
     * @param $onlyActual - show date < now
     * @return mixed
     */
    protected function getQuery($searchQuery, $onlyActive, $onlyActual)
    {
        $entityClass = $this->getEntityClass();
        $entity      = new $entityClass;

        $query['$and'][] = ['type' => $entity->getType()];

        if ($onlyActive) {
            $query['$and'][] = ['is_active' => true];
        }

        if ($onlyActual) {
            $query['$and'][] = ['show_at' => ['$lte' => date('Y-m-d H:i')]];
        }

        if ($searchQuery) {
            $regEx = new \MongoRegex('/' . $searchQuery . '/i');

            $query['$and'][] = ['$or' => [
                ['slug' => $regEx],
                ['name' => $regEx]
            ]];

            return $query;
        }

        return $query;
    }
}