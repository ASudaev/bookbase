<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookControllerTest extends WebTestCase
{
    private const TEST_EXISTING_BOOK_ID = 1;
    private const TEST_EXISTING_AUTHOR_ID = 1;
    private const TEST_EXISTING_BOOK_NAME_EN = 'The Colour of Magic';
    private const TEST_EXISTING_BOOK_NAME_RU = 'Цвет волшебства';
    private const TEST_NEW_BOOK_NAME_RU = 'Тестовый сборник тестовых тестов';
    private const TEST_NEW_BOOK_NAME_EN = 'Test book of testing tests';

    public function testBookByIdSuccess(): void
    {
        $client = static::createClient();
         $client->request('GET', '/book/' . self::TEST_EXISTING_BOOK_ID);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('Id', $data);
        $this->assertArrayHasKey('Name', $data);
        $this->assertArrayHasKey('Author', $data);
        $this->assertEquals(self::TEST_EXISTING_BOOK_ID, $data['Id']);
        $this->assertIsArray($data['Author']);
        $this->assertGreaterThan(0, count($data['Author']));
        $this->assertArrayHasKey('Id', $data['Author'][0]);
        $this->assertArrayHasKey('Name', $data['Author'][0]);
    }

    public function testBookByIdError(): void
    {
        $client = static::createClient();
        $client->request('GET', '/book/0');

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertEquals(0, count($data));
    }

    public function testBookSearchSuccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/book/search/' . self::TEST_EXISTING_BOOK_NAME_EN);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertGreaterThan(0, count($data));

        $this->assertArrayHasKey('Id', $data[0]);
        $this->assertArrayHasKey('Name', $data[0]);
        $this->assertArrayHasKey('Author', $data[0]);
        $this->assertEquals(self::TEST_EXISTING_BOOK_ID, $data[0]['Id']);
        $this->assertTrue(str_contains($data[0]['Name'], self::TEST_EXISTING_BOOK_NAME_RU));
        $this->assertTrue(str_contains($data[0]['Name'], self::TEST_EXISTING_BOOK_NAME_EN));
        $this->assertIsArray($data[0]['Author']);
        $this->assertGreaterThan(0, count($data[0]['Author']));
        $this->assertArrayHasKey('Id', $data[0]['Author'][0]);
        $this->assertArrayHasKey('Name', $data[0]['Author'][0]);
    }

    public function testBookCreateSuccess(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/book/create',
            [
                'name_en' => self::TEST_NEW_BOOK_NAME_EN,
                'name_ru' => self::TEST_NEW_BOOK_NAME_RU,
                'authors' => self::TEST_EXISTING_AUTHOR_ID
            ]
        );

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('Id', $data);
        $this->assertArrayHasKey('Name', $data);
        $this->assertArrayHasKey('Author', $data);
        $this->assertTrue(str_contains($data['Name'], self::TEST_NEW_BOOK_NAME_RU));
        $this->assertTrue(str_contains($data['Name'], self::TEST_NEW_BOOK_NAME_EN));
        $this->assertIsArray($data['Author']);
        $this->assertEquals(1, count($data['Author']));
        $this->assertArrayHasKey('Id', $data['Author'][0]);
        $this->assertArrayHasKey('Name', $data['Author'][0]);
        $this->assertEquals(self::TEST_EXISTING_AUTHOR_ID, $data['Author'][0]['Id']);
    }

    public function testAuthorCreateError(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/book/create',
            [
                'name_en' => self::TEST_NEW_BOOK_NAME_EN,
                'name_ru' => self::TEST_NEW_BOOK_NAME_RU,
                'authors' => '0'
            ]
        );

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
    }
}