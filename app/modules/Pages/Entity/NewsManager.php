<?php
namespace Pages\Entity;

class NewsManager extends PageManager
{
    public function findPublishedBySlug($slug)
    {
        $entityClass = $this->getEntityClass();
        $entity      = new $entityClass;

        return $this->findOne(['slug' => $slug, 'type' => $entity->getType(), 'is_active' => true, 'show_at' => ['$lte' => date('Y-m-d H:i')]]);
    }
}