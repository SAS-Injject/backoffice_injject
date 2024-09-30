<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

  public function __construct(private UserPasswordHasherInterface $passwordHasher){}

  public function load(ObjectManager $manager): void
  {
    $admin = new Users();
  
    $admin->setEmail('lucas.martignon@injject.com');
    $admin->setLastname('Admin');
    $admin->setFirstname('Admin');
    $admin->setUsername("Admin_Admin");
    $admin->setPassword(
        $this->passwordHasher->hashPassword($admin, 'admin')
    );
    $admin->setRoles(["ROLE_USER"]);

    $manager->persist($admin);

    $manager->flush();
  }
}
