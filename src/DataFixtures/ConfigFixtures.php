<?php

namespace App\DataFixtures;

use App\Entity\Configuration;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ConfigFixtures extends Fixture
{

  public function __construct(private UserPasswordHasherInterface $passwordHasher){}

  public function load(ObjectManager $manager): void
  {

    $configs_mandatory = [
      [
        'label' => 'Adresse',
        'value' => '2 Allée des Dahlias',
        'type' => 'string',
      ],
      [
        'label' => 'Code Postal',
        'value' => '33700',
        'type' => 'string',
      ],
      [
        'label' => 'Ville',
        'value' => 'MERIGNAC',
        'type' => 'string',
      ],
      [
        'label' => 'Téléphone',
        'value' => '09 51 99 85 51',
        'type' => 'string',
      ],
      [
        'label' => 'Email',
        'value' => 'reception@injject.com',
        'type' => 'string',
      ],
      [
        'label' => 'Maintenance',
        'value' => '0',
        'type' => 'boolean',
      ],
    ];

    foreach($configs_mandatory as $config) {
      $obj_config = new Configuration();

      $obj_config->setLabel($config['label']);
      $obj_config->setValue($config['value']);
      $obj_config->setIsMandatory(true);
      $obj_config->setType($config['type']);
      $manager->persist($obj_config);
    }

    $manager->flush();
  }
}
