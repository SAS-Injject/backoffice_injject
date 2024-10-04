<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use App\Entity\Configuration;
use App\Entity\Users;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ArticlesFixtures extends Fixture
{

  public function __construct(private UserPasswordHasherInterface $passwordHasher){}

  public function load(ObjectManager $manager): void
  {
    $articles_array = json_decode(file_get_contents('articles2.json'), true);


    foreach($articles_array as $article) {
      $obj_article = new Articles();
      $obj_article->setTitle($article['title']);
      $obj_article->setSummary($article['summary']);
      $obj_article->setContent(json_encode($article['content']));
      $obj_article->setWritenAt((new DateTimeImmutable($article['writen_at'])));
      $obj_article->setModifiedAt((new DateTimeImmutable($article['modified_at'])));
      $obj_article->setPublishedAt((new DateTimeImmutable($article['published_at'])));
      $obj_article->setSeen($article['seen']);
      $obj_article->setIsImportant($article['is_important']);
      $obj_article->setIsPublished($article['is_published']);
      $obj_article->setThumbnailName($article['thumbnail_name']);
      $obj_article->setThumbnailFile($article['thumbnail_file']);
      $obj_article->setThumbnailAlt($article['thumbnail_alt']);
      $obj_article->setThumbnailLegend($article['thumbnail_legend']);

      $manager->persist($obj_article);
    }

    $manager->flush();
  }
}
