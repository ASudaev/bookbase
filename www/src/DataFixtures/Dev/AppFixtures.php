<?php

namespace App\DataFixtures\Dev;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private const TEST_AUTHORS = [
        1 => ['name' => 'Терри Пратчетт'],
        2 => ['name' => 'Нил Гейман'],
    ];

    private const TEST_BOOKS = [
        [
            'name_en' => 'The Colour of Magic',
            'name_ru' => 'Цвет волшебства',
            'authors' => [1]
        ],
        [
            'name_en' => 'Good Omens',
            'name_ru' => 'Благие знамения',
            'authors' => [1, 2]
        ],
        [
            'name_en' => 'American Gods',
            'name_ru' => 'Американские боги',
            'authors' => [1]
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $authors = [];

        foreach (self::TEST_AUTHORS as $authorId => $authorData)
        {
            $authors[$authorId] = new Author();
            $authors[$authorId]->setName($authorData['name']);
            $manager->persist($authors[$authorId]);
        }

        foreach (self::TEST_BOOKS as $bookData)
        {
            $book = new Book();
            $book->translate('ru')->setName($bookData['name_ru']);
            $book->translate('en')->setName($bookData['name_en']);
            foreach ($bookData['authors'] as $authorId)
            {
                $book->addAuthor($authors[$authorId]);
            }
            $manager->persist($book);
            $book->mergeNewTranslations();
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
