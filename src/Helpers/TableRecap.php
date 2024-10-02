<?php

namespace App\Helpers;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class TableRecap {

  public static function make_recap(string $title, ServiceEntityRepository $repository, $icon, array $criteria = [], string $item_name = "") {
    
    if(empty($item_name)) {
      $item = explode("\\", $repository->getEntityName());
      $item = array_pop($item);
  
      if(substr($item, -1, 1) === "s") {
        $item = substr($item, 0, -1);
      }
    } else {
      $item = $item_name;
    }

    if(count($criteria) > 0) {
      return [
        "title" => $title,
        "value" => count($repository->findBy($criteria)),
        "icon" => $icon->value,
        "item" => $item
      ];
    }

    return [
      "title" => $title,
      "value" => count($repository->findAll()),
      "icon" => $icon->value,
      "item" => $item
    ];
  }

}