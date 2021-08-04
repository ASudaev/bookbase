<?php

namespace App\DataFixtures\Dev;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private const TEST_SURNAMES = ['Иванов', 'Смирнов', 'Кузнецов', 'Попов', 'Васильев', 'Петров', 'Соколов',
                                   'Михайлов', 'Новиков', 'Фёдоров', 'Морозов', 'Волков', 'Алексеев', 'Лебедев',
                                   'Семенов', 'Егоров', 'Павлов', 'Козлов', 'Степанов', 'Николаев', 'Орлов', 'Андреев',
                                   'Макаров', 'Никитин', 'Захаров'];

    private const TEST_1STNAMES = ['m' => ['Александр', 'Алексей', 'Виктор', 'Дмитрий', 'Илья', 'Кирилл', 'Максим',
                                           'Михаил', 'Роман', 'Степан',],
                                   'f' => ['Александра', 'Анастасия', 'Анна', 'Вероника', 'Виктория', 'Дарья',
                                           'Екатерина', 'Елизавета', 'Мария', 'Полина',]];

    private const TEST_2NDNAMES = ['Александров', 'Алексеев', 'Андреев', 'Артемов', 'Викторов', 'Владимиров',
                                   'Даниилов', 'Дмитриев', 'Егоров', 'Иванов', 'Кириллов', 'Максимов', 'Марков',
                                   'Михаилов', 'Петров', 'Романов', 'Сергеев', 'Степанов', 'Тимофеев', 'Ярославов'];

    private const TEST_WORDS_RU = [['Лес', 'Волна', 'Путь', 'Место', 'Академия', 'Дом', 'Чудо', 'Возвращение', 'Герой',
                                    'Тайна', 'Природа', 'Дверь', 'Убийство', 'Сигнал', 'Эффект', 'Война', 'Гость',
                                    'Жизнь', 'План', 'Кресло', 'Девушка', 'Зона', 'Тишина', 'День', 'Ожидание'],
                                   ['неизвестной', 'крошечной', 'закрытой', 'розовой', 'круглой', 'быстрой', 'важной',
                                    'талантливой', 'нормальной', 'бедной', 'вечерней', 'необычной', 'серебряной',
                                    'больной', 'нехорошей', 'таинственной', 'зелёной', 'священной', 'глупой',
                                    'уютной',],
                                   ['женщины', 'профессии', 'карты', 'ошибки', 'лодки', 'страны', 'улицы', 'комнаты',
                                    'собаки', 'кошки', 'дружбы', 'толпы', 'горы', 'рыбы', 'сумки', 'девочки', 'руки',
                                    'фотографии', 'скорости', 'программы',]];

    private const TEST_WORDS_EN = [['forest', 'wave', 'path', 'place', 'academy', 'house', 'miracle', 'return', 'hero',
                                    'mystery', 'nature', 'door', 'murder', 'signal', 'effect', 'war', 'guest', 'life',
                                    'plan', 'chair', 'girl', 'zone', 'silence', 'day', 'waiting'],
                                   ['unknown', 'tiny', 'closed', 'pink', 'round', 'quick', 'important', 'talented',
                                    'normal', 'poor', 'evening', 'unusual', 'silver', 'unhealthy', 'bad', 'mysterious',
                                    'green', 'holy', 'dumb', 'cozy',],
                                   ['lady', 'profession', 'map', 'error', 'boat', 'country', 'street', 'room', 'dog',
                                    'cat', 'friendship', 'crowd', 'mountain', 'fish', 'bag', 'girl', 'hand', 'photo',
                                    'speed', 'program',]];

    private const TEST_ARTICLES = ['A', 'The'];

    private const TEST_PREPOSITIONS_EN = ['of', 'for'];

    private const TEST_PREPOSITIONS_RU = [' ', ' для '];

    public function load(ObjectManager $manager)
    {
        $authors = $this->makeAuthors($manager);

        $this->makeBooks($manager, $authors);
    }

    private function makeAuthors(ObjectManager $manager): array
    {
        $authors = [];

        foreach (self::TEST_SURNAMES as $surname)
        {
            foreach (self::TEST_2NDNAMES as $secondName)
            {
                foreach (self::TEST_1STNAMES as $firstNameGender => $firstNames)
                {
                    foreach ($firstNames as $firstName)
                    {
                        $fullName = $surname . ($firstNameGender === 'f' ? 'а' : '') . ' '
                            . $firstName . ' '
                            . $secondName . ($firstNameGender === 'f' ? 'на' : 'ич');

                        $author = new Author();
                        $author->setName($fullName);
                        $authors[] = $author;

                        $manager->persist($author);
                    }
                }
            }

            $manager->flush();
        }

        return $authors;
    }

    private function makeBooks(ObjectManager $manager, array $authors): void
    {
        foreach (self::TEST_WORDS_RU[0] as $wordOneIndex => $wordOne)
        {
            foreach (self::TEST_WORDS_RU[1] as $wordTwoIndex => $wordTwo)
            {
                foreach (self::TEST_WORDS_RU[2] as $wordThreeIndex => $wordThree)
                {
                    $prepositionId = mt_rand(0, count(self::TEST_PREPOSITIONS_EN) - 1);
                    $nameRu = $wordOne
                        . self::TEST_PREPOSITIONS_RU[$prepositionId]
                        . $wordTwo . ' '
                        . $wordThree;
                    $nameEn = self::TEST_ARTICLES[mt_rand(0, count(self::TEST_ARTICLES) - 1)] . ' '
                        . self::TEST_WORDS_EN[0][$wordOneIndex] . ' '
                        . self::TEST_PREPOSITIONS_EN[$prepositionId] . ' '
                        . self::TEST_WORDS_EN[1][$wordTwoIndex] . ' '
                        . self::TEST_WORDS_EN[2][$wordThreeIndex];
                    $authorId = mt_rand(0, count($authors) - 1);

                    $book = new Book();
                    $book->translate('ru')->setName($nameRu);
                    $book->translate('en')->setName($nameEn);
                    $book->addAuthor($authors[$authorId]);

                    if (mt_rand(0, 5) === 1)
                    {
                        $authorId2 = mt_rand(0, count($authors) - 1);
                        if ($authorId2 !== $authorId)
                        {
                            $book->addAuthor($authors[$authorId2]);
                        }
                    }

                    $manager->persist($book);
                    $book->mergeNewTranslations();
                }
            }

            $manager->flush();
        }
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
