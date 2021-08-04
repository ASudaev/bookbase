<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthorControllerTest extends WebTestCase
{
    private const TEST_EXISTING_AUTHOR_NAME = 'Терри Пратчетт';
    private const TEST_NEW_AUTHOR_NAME = 'Тест Тестович Тестов';

    /**
     * Тестирует запрос /author/{id} с заданным ID
     *
     * @param KernelBrowser $client
     * @param int $id
     * @param string $name
     */
    private function testAuthorByIdSuccess(KernelBrowser $client, int $id, string $name): void
    {
        $client->request('GET', '/author/' . $id);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('Id', $data);
        $this->assertArrayHasKey('Name', $data);
        $this->assertEquals($id, $data['Id']);
        $this->assertEquals($name, $data['Name']);
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
        $this->assertEquals(self::TEST_EXISTING_AUTHOR_NAME, $data[0]['Name']);

        $this->testAuthorByIdSuccess($client, $data[0]['Id'], $data[0]['Name']);
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
