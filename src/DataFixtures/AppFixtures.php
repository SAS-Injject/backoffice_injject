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

    $users_array = json_decode(file_get_contents('users.json'), true);
    foreach($users_array as $index => $user) {
      $admin = new Users();
    
      $admin->setEmail($user['email']);
      $admin->setLastname($user['lastname']);
      $admin->setFirstname($user['firstname']);

      $username = strtolower($user['firstname']) ."." . strtolower($user['lastname']);
      $admin->setUsername($username);

      $password = $this->generateRandomString();
      $admin->setPassword(
          $this->passwordHasher->hashPassword($admin, $password)
      );
      $admin->setRoles(["ROLE_USER"]);

      $users_array[$index]['password'] = $password;

      $manager->persist($admin);
    }

    file_put_contents('users.json', json_encode($users_array, JSON_PRETTY_PRINT));

    $manager->flush();
  }

  private function generateRandomString($length = 20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $special_characters = '&?!$%';
    $randomString = str_shuffle(
      substr(
        substr(
          str_shuffle($special_characters), 0, 2
        ) . str_shuffle($characters), 0, $length
      )
    );
    return $randomString;
}
}
