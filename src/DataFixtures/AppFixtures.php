<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $entityManager)
    {
        $user = new User();
        $user->setEmail('admin@admin.fr');
        $user->setPseudo('admin');
        $user->setFirstName('Admin');
        $user->setLastName('Admin');
        $user->setIsVerified(1);
        $passwordHash = password_hash('admin', PASSWORD_DEFAULT);
        $user->setPassword($passwordHash);

        $entityManager->persist($user);
        $entityManager->flush();

        $category1 = new Category();
        $category2 = new Category();
        $category1->setLabel('Test 1');
        $category2->setLabel('Test 2');

        $entityManager->persist($category1);
        $entityManager->persist($category2);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Mute');
        $trick->setDescription('Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.');
        $trick->setSlug('mute');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('mute-6266d15f34bd6.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Sad');
        $trick->setDescription('Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.');
        $trick->setSlug('sad');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('sad-6266d1b03aa92.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Indy');
        $trick->setDescription('Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.');
        $trick->setSlug('indy');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('indy-6266d1eb5aae5.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Stalefish');
        $trick->setDescription('Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.');
        $trick->setSlug('stalefish');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('stalefish-6266d21735459.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Tail grab');
        $trick->setDescription('Saisie de la partie arrière de la planche, avec la main arrière.');
        $trick->setSlug('tail-grab');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('tailgrab-6266d24166de9.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Nose grab');
        $trick->setDescription('Saisie de la partie avant de la planche, avec la main avant.');
        $trick->setSlug('nose-grab');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('nose-grab-6266d2779eb32.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Japan air');
        $trick->setDescription('Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.');
        $trick->setSlug('japan-air');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('japan-air-6266d29b7bdc9.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Seat belt');
        $trick->setDescription('Saisie du carre frontside à l\'arrière avec la main avant.');
        $trick->setSlug('seat-belt');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('seat-belt-6266d2c691613.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Truck driver');
        $trick->setDescription('Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture).');
        $trick->setSlug('truck-driver');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('truck-driver-1-5eeb14e542cc0-6266d2ecb3e4a.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        $trick = new Trick();
        $trick->setIdCategory($category1);
        $trick->setIdUser($user);
        $trick->setTitle('Rotation');
        $trick->setDescription(`On désigne par le mot « rotation » uniquement des rotations horizontales ; les rotations verticales sont des flips. Le principe est d'effectuer une rotation horizontale pendant le saut, puis d'attérir en position switch ou normal. La nomenclature se base sur le nombre de degrés de rotation effectués  :
            un 180 désigne un demi-tour, soit 180 degrés d'angle ;
            360, trois six pour un tour complet ;
            540, cinq quatre pour un tour et demi ;
            720, sept deux pour deux tours complets ;
            900 pour deux tours et demi ;
            1080 ou big foot pour trois tours ;
            etc.`);
        $trick->setSlug('rotation');
        $trick->setCreatedAt(new \DateTimeImmutable());
        $trick->setFeaturedPicture('rota-6266edf48121a.jpg');
        $entityManager->persist($trick);
        $entityManager->flush();

        for ($i = 1; $i < 21; $i++) {
            $comment = new Comment();
            $comment->setUser($user);
            $comment->setComment('Commentaire n° : ' . $i);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setTrick($trick);
            $manager->persist($comment);
        }

        $entityManager->flush();
    }
}