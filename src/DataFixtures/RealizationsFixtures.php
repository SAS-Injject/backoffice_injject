<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use App\Entity\Configuration;
use App\Entity\Realization;
use App\Entity\Users;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RealizationsFixtures extends Fixture
{

  public function __construct(private UserPasswordHasherInterface $passwordHasher){}

  public function load(ObjectManager $manager): void
  {
    $realisations_array = json_decode(file_get_contents('realizations.json'), true);


    foreach($realisations_array as $realisation) {
      $obj_realisation = new Realization();

      $obj_realisation->setTitle($realisation['title']);
      $obj_realisation->setPeriod(new DateTimeImmutable($realisation['period']));
      $obj_realisation->setDuration($realisation['duration']);
      $obj_realisation->setClientView($realisation['client_view']);
      $obj_realisation->setIsPublished($realisation['is_published']);
      $obj_realisation->setContext($realisation['context']);
      $obj_realisation->setTask($realisation['task']);
      $obj_realisation->setAnswer($realisation['answer']);

      $manager->persist($obj_realisation);
    }

    $manager->flush();
  }
}
