<?php

namespace App\Tests\Controller;

use App\Tests\Traits\CheckForQueryError;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookControllerTest extends WebTestCase
{
    use CheckForQueryError;

    private const TEST_EXISTING_BOOK_NAME_EN = 'The Colour of Magic';
    private const TEST_EXISTING_BOOK_NAME_RU = 'Цвет волшебства';
    private const TEST_EXISTING_AUTHOR_NAME = 'Терри Пратчетт';
    private const TEST_NEW_BOOK_NAME_RU = 'Тестовый сборник тестовых тестов';
    private const TEST_NEW_BOOK_NAME_EN = 'Test book of testing tests';

    public function testBookByIdError(): void
    {
        $client = static::createClient();

        $client->request('GET', '/ru/book/0');
        $this->checkForEmptyResult($client);

        $client->request('GET', '/en/book/0');
        $this->checkForEmptyResult($client);
    }

    public function testBookSearchSuccess(): void
    {
        $client = static::createClient();


        $client->request('GET', '/book/search/' . self::TEST_EXISTING_BOOK_NAME_EN);
        $id = $this->checkBookSearchResponse($client, self::TEST_EXISTING_BOOK_NAME_EN);
        if ($id)
        {
            $this->checkBookByIdSuccess($client, $id, self::TEST_EXISTING_BOOK_NAME_RU, self::TEST_EXISTING_BOOK_NAME_EN);
        }

        $client->request('GET', '/book/search/' . self::TEST_EXISTING_BOOK_NAME_RU);
        $id = $this->checkBookSearchResponse($client, self::TEST_EXISTING_BOOK_NAME_RU);
        if ($id)
        {
            $this->checkBookByIdSuccess($client, $id, self::TEST_EXISTING_BOOK_NAME_RU, self::TEST_EXISTING_BOOK_NAME_EN);
        }
    }

    private function getAuthorId(KernelBrowser $client, string $name)
    {
        $client->request('GET', '/author/search/' . $name);

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertGreaterThan(0, count($data));

        $this->assertArrayHasKey('Id', $data[0]);

        return $data[0]['Id'] ?? null;
    }

    public function testBookCreateSuccess(): void
    {
        $client = static::createClient();

        $authorId = $this->getAuthorId($client, self::TEST_EXISTING_AUTHOR_NAME);

        if (!$authorId)
        {
            return;
        }

        $client->request(
            'POST',
            '/book/create',
            [
                'name_en' => self::TEST_NEW_BOOK_NAME_EN,
                'name_ru' => self::TEST_NEW_BOOK_NAME_RU,
                'authors' => $authorId
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
        $this->assertTrue(
            str_contains($data['Name'], self::TEST_NEW_BOOK_NAME_RU)
            || str_contains($data['Name'], self::TEST_NEW_BOOK_NAME_EN)
        );
        $this->assertIsArray($data['Author']);
        $this->assertEquals(1, count($data['Author']));
        $this->assertArrayHasKey('Id', $data['Author'][0]);
        $this->assertArrayHasKey('Name', $data['Author'][0]);
        $this->assertEquals($authorId, $data['Author'][0]['Id']);
    }

    public function testBookCreateError(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/book/create',
            [
                'name_en' => self::TEST_NEW_BOOK_NAME_EN,
                'name_ru' => '',
                'authors' => '0'
            ]
        );
        $this->checkForErrorResult($client);

        $client->request(
            'POST',
            '/book/create',
            [
                'name_en' => '',
                'name_ru' => self::TEST_NEW_BOOK_NAME_RU,
                'authors' => '0'
            ]
        );
        $this->checkForErrorResult($client);

        $client->request(
            'POST',
            '/book/create',
            [
                'name_en' => self::TEST_NEW_BOOK_NAME_EN,
                'name_ru' => self::TEST_NEW_BOOK_NAME_RU,
                'authors' => '0'
            ]
        );
        $this->checkForErrorResult($client);
    }

    private function checkBookSearchResponse(KernelBrowser $client, string $name): ?int
    {
        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertGreaterThan(0, count($data));

        $this->assertArrayHasKey('Id', $data[0]);
        $this->assertArrayHasKey('Name', $data[0]);
        $this->assertArrayHasKey('Author', $data[0]);
        $this->assertEquals($name, $data[0]['Name']);
        $this->assertIsArray($data[0]['Author']);
        $this->assertGreaterThan(0, count($data[0]['Author']));
        $this->assertArrayHasKey('Id', $data[0]['Author'][0]);
        $this->assertArrayHasKey('Name', $data[0]['Author'][0]);

        return $data[0]['Id'] ?? null;
    }

    /**
     * Тестирует запросы /{_locale}/book/{id} с заданным ID для локалей ru и en
     *
     * @param KernelBrowser $client
     * @param int $id
     * @param string $name_ru
     * @param string $name_en
     */
    private function checkBookByIdSuccess(KernelBrowser $client, int $id, string $name_ru, string $name_en): void
    {
        $client->request('GET', '/ru/book/' . $id);
        $this->checkBookByIdResponse($client, $id, $name_ru);

        $client->request('GET', '/en/book/' . $id);
        $this->checkBookByIdResponse($client, $id, $name_en);
    }

    /**
     * @param KernelBrowser $client
     * @param int $id
     * @param string $name
     */
    private function checkBookByIdResponse(KernelBrowser $client, int $id, string $name)
    {
        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('Id', $data);
        $this->assertArrayHasKey('Name', $data);
        $this->assertArrayHasKey('Author', $data);
        $this->assertEquals($id, $data['Id']);
        $this->assertEquals($name, $data['Name']);
        $this->assertIsArray($data['Author']);
        $this->assertGreaterThan(0, count($data['Author']));
        $this->assertArrayHasKey('Id', $data['Author'][0]);
        $this->assertArrayHasKey('Name', $data['Author'][0]);
    }
}
