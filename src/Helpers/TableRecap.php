<?php

namespace App\Helpers;

class TableRecap {

  public static function make_recap(string $title, $repository, $icon, array $criteria = []) {
    if(count($criteria) > 0) {
      return [
        "title" => $title,
        "value" => count($repository->findBy($criteria)),
        "icon" => $icon->value
      ];
    }

    return [
      "title" => $title,
      "value" => count($repository->findAll()),
      "icon" => $icon->value
    ];
  }

}