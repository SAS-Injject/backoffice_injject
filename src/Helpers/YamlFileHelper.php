<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Yaml\Yaml;

class YamlFileHelper {

  public static function getYAMLContent(string $file_path): array | null{
    if(!file_exists($file_path)) {
      return new FileException($file_path. ' not found');
    }
    return isset($file_path) ? Yaml::parse(file_get_contents($file_path)) : [];
  } 

  public static function setYAMLContent(string $file_path, array $data) {
    isset($file_path) ? file_put_contents($file_path, Yaml::dump($data)) : [];
  } 

  public static function getAllYAMLContent(array $file_path): array {
    $yaml_data_array = [];
    foreach($file_path as $file) {
      $yaml_data_array = array_merge_recursive($yaml_data_array, Yaml::parse(file_get_contents($file), YAML::PARSE_OBJECT));
    }
    return $yaml_data_array;
  }

  public static function getAllYamlConfigFile(string $files_name, string $path): array {

    $file = '/config/'.$files_name.'.yaml';
    $mod_config = '/config/config.yaml';
    $list = [];

    if(file_exists($path.$file)) 
      $list[] = $path.$file;

    $controller_dir = scandir($path);
    foreach($controller_dir as $info_dir) {
      if(!in_array($info_dir, ['.', '..', '.gitignore'])) {
        $sub_dir = '/'.$info_dir;
        
        if(is_dir($path.$sub_dir)) {
          if(file_exists($path.$sub_dir.$file)) 
            $list[] = $path.$sub_dir.$file;
        }
      }
    }

    return $list;
  }

  public static function getYamlConfigFile(string $files_name, string $path): string {

    $file = '/config/'.$files_name.'.yaml';
    $list = "";

    if(file_exists($path.$file)) 
      $list = $path.$file;

    return $list;
  }

}