<?php

/**
 * Created by PhpStorm.
 * User: mpfister
 * Date: 03/10/2016
 * Time: 12:36
 */

namespace Model;

use OCFram\Manager;
use \Entity\News;

abstract class NewsManager extends Manager {
    abstract public function getList($debut = -1,$limite = -1);
    abstract public function getNews($id);
    abstract public function count();
    abstract public function add(News $news);
    abstract public function modify(News $news);
    abstract public function delete($id);

    public function save(News $news){
        if($news->isValid()){
            if($news->isNew()){
                $this->add($news);
            }
            else{
                $this->modify($news);
            }
        }
        else {
            throw new \RuntimeException('La news doit être validée pour être enregistrée');
        }
    }
}