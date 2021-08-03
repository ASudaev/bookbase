<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthorControllerTest extends WebTestCase
{
    private const TEST_EXISTING_AUTHOR_NAME = 'Терри Пратчетт';
    private const TEST_EXISTING_AUTHOR_ID = 1;
    private const TEST_NEW_AUTHOR_NAME = 'Тест Тестович Тестов';

    public function testAuthorByIdSuccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/author/' . self::TEST_EXISTING_AUTHOR_ID);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('Id', $data);
        $this->assertArrayHasKey('Name', $data);
        $this->assertEquals(self::TEST_EXISTING_AUTHOR_ID, $data['Id']);
    }

    public function testAuthorByIdError(): void
    {
        $client = static::createClient();
        $client->request('GET', '/author/0');

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertEquals(0, count($data));
    }

    public function testAuthorSearchSuccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/author/search/' . self::TEST_EXISTING_AUTHOR_NAME);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertGreaterThan(0, count($data));

        $this->assertArrayHasKey('Id', $data[0]);
        $this->assertArrayHasKey('Name', $data[0]);
        $this->assertEquals(self::TEST_EXISTING_AUTHOR_ID, $data[0]['Id']);
        $this->assertEquals(self::TEST_EXISTING_AUTHOR_NAME, $data[0]['Name']);
    }

    public function testAuthorCreateSuccess(): void
    {
        $client = static::createClient();
        $client->request('POST', '/author/create', ['name' => self::TEST_NEW_AUTHOR_NAME]);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('Id', $data);
        $this->assertArrayHasKey('Name', $data);
        $this->assertEquals(self::TEST_NEW_AUTHOR_NAME, $data['Name']);
    }

    public function testAuthorCreateError(): void
    {
        $client = static::createClient();
        $client->request('POST', '/author/create', ['name' => self::TEST_EXISTING_AUTHOR_NAME]);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
    }
}
