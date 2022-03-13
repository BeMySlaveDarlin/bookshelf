<?php

namespace App\Tests\Feature;

use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorCrudTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/author');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->isJson();
    }

    public function testCreateSuccess(): void
    {
        $client = static::createClient();

        $client->request('POST', '/en/author/create', [
            'name' => 'John Doe #' . \time(),
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->isJson();
    }

    public function testCreateBadRequest(): void
    {
        $client = static::createClient();

        $client->request('POST', '/en/author/create', []);
        $this->assertResponseStatusCodeSame(500);
        $this->isJson();
    }

    public function testGetSuccess(): void
    {
        $client = static::createClient();

        $authorRepository = static::getContainer()->get(AuthorRepository::class);
        $author = $authorRepository->findFirst();
        $client->request('GET', "/en/author/{$author->getId()}");
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->isJson();
    }

    public function testGetNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/en/author/0', []);
        $this->assertResponseStatusCodeSame(404);
        $this->isJson();
    }

    public function testUpdateSuccess(): void
    {
        $client = static::createClient();

        $authorRepository = static::getContainer()->get(AuthorRepository::class);
        $author = $authorRepository->findFirst();
        $client->request('PATCH', "/en/author/{$author->getId()}", [
            'name' => 'John Doe #' . \time(),
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->isJson();
    }

    public function testUpdateBadRequest(): void
    {
        $client = static::createClient();

        $authorRepository = static::getContainer()->get(AuthorRepository::class);
        $author = $authorRepository->findFirst();
        $client->request('PATCH', "/en/author/{$author->getId()}");
        $this->assertResponseStatusCodeSame(500);
        $this->isJson();
    }

    public function testDeleteSuccess(): void
    {
        $client = static::createClient();

        $authorRepository = static::getContainer()->get(AuthorRepository::class);
        $author = $authorRepository->findFirst();
        $client->request('DELETE', "/en/author/{$author->getId()}");
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->isJson();
    }

    public function testDeleteNotFound(): void
    {
        $client = static::createClient();

        $client->request('DELETE', "/en/author/0");
        $this->assertResponseStatusCodeSame(404);
        $this->isJson();
    }
}
