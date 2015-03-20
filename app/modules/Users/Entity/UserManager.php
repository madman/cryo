<?php

namespace Users\Entity;

use Core\Db\Manager;

class UserManager extends Manager
{
    protected $collection = 'users';

    public function findByEmail($email)
    {
        return $this->findOne(['email' => $email]);
    }

    public function findManyByEmail($email)
    {
        return $this->find(['email' => $email]);
    }

    public function findUserBySocialIdentity($indentity)
    {
        return $this->findOne(['social_identity' => $indentity]);
    }

    public function findByRemindCode($code)
    {
        return $this->findOne(['remind_code' => $code]);
    }

    public function updateLastActionAt(User $user)
    {
        $this->update(['_id' => new \MongoId($user->getId())], ['$set' => ['last_action_at' => new \MongoDate]]);
    }

    public function findAllByEmail($email)
    {
        return $this->find(['email' => new \MongoRegex('/' . $email . '/i')]);
    }

    /**
     * @param      $searchQuery
     * @param      $currentPage
     * @param null $itemsPerPage
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function search($searchQuery, $currentPage, $itemsPerPage = null)
    {
        $query = $this->getQuery($searchQuery);

        $itemsPerPage = ($itemsPerPage) ?: $this->app->config->get('items_per_page')['backend'];

        $searchResult = $this->find($query)
            ->skip(($currentPage - 1) * $itemsPerPage)
            ->limit($itemsPerPage);

        return [$searchResult, ceil($this->find($query)->count() / $itemsPerPage)];
    }

    /**
     * @param $searchQuery
     *
     * @return mixed
     */
    protected function getQuery($searchQuery)
    {
        $query = [];

        if ($searchQuery) {
            $regEx = new \MongoRegex('/' . $searchQuery . '/i');

            $tofind   = [];
            $tofind[] = ['social_identity' => $regEx];
            $tofind[] = ['email' => $regEx];

            if ($this->isValidMongoID($searchQuery)) {
                $tofind[] = ['_id' => new \MongoId($searchQuery)];
            }

            $query = ['$or' => $tofind];
        }

        return $query;
    }

    private function isValidMongoID($str)
    {
        // A valid Object Id must be 24 hex characters
        return preg_match('/^[0-9a-fA-F]{24}$/', $str);
    }
}
