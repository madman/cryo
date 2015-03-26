<?php

namespace Startpage\Controller\Frontend;

use Core\Controller;

class IndexController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
