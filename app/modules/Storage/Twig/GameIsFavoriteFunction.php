<?php

namespace Games\Twig;

use Games\Helper\CacheGameCountsEntity;
use Games\Entity\Game;

class GameIsFavoriteFunction extends \Twig_SimpleFunction
{
    public function  __construct($options = [])
    {
        parent::__construct($this->getName(), $this->getCallable(), $options);
    }

    public function getName()
    {
        return 'game_is_favorite';
    }

    public function getCallable()
    {
        return function ($user_id, Game $game) {
            return CacheGameCountsEntity::find($user_id)->isFavorite($game);
        };
    }
}
