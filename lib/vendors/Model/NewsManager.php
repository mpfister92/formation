<?php

/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 12:36
 */

namespace Model;

use OCFram\Manager;

abstract class NewsManager extends Manager {
    abstract public function getList($debut = -1,$limite = -1);
    abstract public function getNews($id);
}