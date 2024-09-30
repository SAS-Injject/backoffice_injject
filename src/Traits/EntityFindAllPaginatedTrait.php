<?php

namespace App\Traits;

use Doctrine\ORM\Tools\Pagination\Paginator;

trait EntityFindAllPaginatedTrait {
  public function findAllPaginated(int $page, int $limit = 20, array $criteria = []): array {
    
    $limit = abs($limit);
    $result = [];
    $query = $this->createQueryBuilder('r');
    if(count($criteria) > 0) {
      foreach($criteria as $clause => $value) {
        $query->andWhere("r.$clause = $value");
      }
    }
    $query->setMaxResults($limit)
      ->setFirstResult(($page * $limit) - $limit);
    
    $paginator = new Paginator($query, false);
    $data = $paginator->getQuery()->getResult();
  
    //On rempli le tableau
    $result['data'] = [];
    $result['pages'] = 0;
    $result['page'] = $page;
    $result['limit'] = $limit;
  
    //On vérifie qu'on a des données
    if(empty($data)){
      return $result;
    }
  
    //On calcule le nombre de pages
    $pages = ceil($paginator->count() / $limit);
  
    //On rempli le tableau
    $result['data'] = $data;
    $result['pages'] = $pages;
    $result['page'] = $page;
    $result['limit'] = $limit;
  
    return $result;
  }
}

