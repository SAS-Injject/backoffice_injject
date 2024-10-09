<?php


namespace App\Services;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{

  private $params;

  public function __construct(ParameterBagInterface $params)
  {
    $this->params = $params;
  }

  public function add(UploadedFile $thumbnail, ?string $folder = "/assets/imgs/others", ?int $width = 300, ?int $height = 300) 
  {
    // On donne un nouveau nom à l'image
    $file = md5(uniqid('tn_', true)) . '.webp';

    // On récupère les infos de l'image
    $picture_infos = getimagesize($thumbnail);
    if($picture_infos === false) {
      throw new Exception('Format d\'image incorrect');
    }

    // On vérifie le format de l'image
    switch($picture_infos['mime']) {
      case 'image/png':
        $picture_source = imagecreatefrompng($thumbnail);
        break;
      case 'image/jpeg':
        $picture_source = imagecreatefromjpeg($thumbnail);
        break;
      case 'image/webp':
        $picture_source = imagecreatefromwebp($thumbnail);
        break;
      default:
        throw new Exception('Format d\'image incorrect');
        break;
    }

    // On recadre l'image
      //On récupère les dimensions
    // $image_width = $picture_infos[0];
    // $image_height = $picture_infos[1];

    //   //On vérifie l'orientation
    //   switch($image_width <=> $image_height) {
    //     case -1: // Portrait
    //       $square_size = $image_width;
    //       $src_x = 0;
    //       $src_y = ($image_height - $square_size) / 2;
    //       break;
    //     case 0: // Carré
    //       $square_size = $image_width;
    //       $src_x = 0;
    //       $src_y = 0;
    //       break;
    //     case 1: // Paysage
    //       $square_size = $image_height;
    //       $src_x = ($image_width - $square_size) / 2;
    //       $src_y = 0;
    //       break;
    //   }

      // On crée une nouvelle image "vierge"
      // $resized_picture = imagecreatetruecolor($width, $height);
      // imagecopyresampled($resized_picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $square_size, $square_size);

      $path = $this->params->get('imgs_directory') . $folder;

      // On crée le dossier de destination s'il n'existe pas
      // if(!file_exists($path . '/mini/')) {
      //   mkdir($path . '/mini/', 0755, true);
      // }

      // On stocke la nouvelle image
      // imagewebp($resized_picture, $path . '/mini/' . $width.'-'.$height.'_'.$file);
      $thumbnail->move($path . '/', $file);

      return [ 
        "origin" => strstr($path.'/'.$file, '/assets'), 
        // "mini" => strstr($path . '/mini/' . $width.'-'.$height.'_'.$file, '/assets'), 
        "name" => explode('.', $file)[0]
      ];
  }

  public function delete(string $file, ?string $folder = '', ?int $width = 300, ?int $height = 300) {
    $success = false;

    $path = $this->params->get('imgs_directory') . $folder;

    $original = $path . '/' . $file;
    if(file_exists($original)) {
      unlink($original);
      $success = true;
    }

    return $success;
  }

  public static function base64_to_image($base64_string, $output_path, $output_file) {

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    $type = explode(';', $data[0]);
    $type = explode(':', $type[0]);
    $type = explode('/', $type[1]);

    if($type[0] === 'image') {

      if(!is_dir($output_path)) {
        mkdir($output_path, 0777, true);
      }

      // open the output file for writing
      $ifp = fopen( $output_path.$output_file.'.'.$type[1], 'wb' ); 

      // we could add validation here with ensuring count( $data ) > 1
      fwrite( $ifp, base64_decode( $data[ 1 ] ) );

      // clean up the file resource
      fclose( $ifp ); 

      $output = explode('/', $output_file);
      $output = array_pop($output);

      return $output.'.'.$type[1]; 
    }

    return false;
  }

}