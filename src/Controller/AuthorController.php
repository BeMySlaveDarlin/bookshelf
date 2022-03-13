<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Response\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    private AuthorRepository $authorRepository;
    private ResponseFactory $formatter;

    public function __construct(AuthorRepository $authorRepository, ResponseFactory $responseFormatter)
    {
        $this->authorRepository = $authorRepository;
        $this->formatter = $responseFormatter;
    }

    /**
     * @Route("/{lang}/author/search", name="app_author_search", methods={"GET"})
     */
    public function search(string $lang, Request $request): JsonResponse
    {
        try {
            $authors = $this->authorRepository->findByName(
                (string) $request->query->get('name', 'author'),
                (int) $request->query->get('page', 1),
                (int) $request->query->get('maxResults', 10),
            );

            return $this->formatter->createSuccessResponse($lang, $this->authorRepository->toArray($authors));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($lang, $throwable);
        }
    }

    /**
     * @Route("/{lang}/author/create", name="app_author_create", methods={"POST"})
     */
    public function create(string $lang, Request $request): JsonResponse
    {
        try {
            $author = $this->authorRepository->create((string) $request->request->get('name'), $lang);

            $this->authorRepository->add($author);

            return $this->formatter->createSuccessResponse($lang, $author->toArray($lang));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($lang, $throwable);
        }
    }

    /**
     * @Route("/{lang}/author/{id}", name="app_author_view", methods={"GET"})
     */
    public function view(string $lang, int $id): JsonResponse
    {
        try {
            $author = $this->authorRepository->findOneById($id);

            return $this->formatter->createSuccessResponse($lang, $author->toArray($lang));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($lang, $throwable);
        }
    }

    /**
     * @Route("/{lang}/author/{id}", name="app_author_update", methods={"PATCH"})
     */
    public function update(string $lang, int $id, Request $request): JsonResponse
    {
        try {
            $author = $this->authorRepository->findOneById($id);
            $author->translate($lang)->setName($request->request->get('name'));

            $this->authorRepository->add($author);

            return $this->formatter->createSuccessResponse($lang, $author->toArray($lang));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($lang, $throwable);
        }
    }

    /**
     * @Route("/{lang}/author/{id}", name="app_author_delete", methods={"DELETE"})
     */
    public function delete(string $lang, int $id): JsonResponse
    {
        try {
            $author = $this->authorRepository->findOneById($id);

            $this->authorRepository->remove($author);

            return $this->formatter->createSuccessResponse($lang, [], \sprintf('Author %s deleted', $id));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($lang, $throwable);
        }
    }

    /**
     * @Route("/{lang}/author", name="app_author_index", methods={"GET"})
     */
    public function index(string $lang): JsonResponse
    {
        return new JsonResponse([
            'lang' => $lang,
            'status' => Response::HTTP_OK,
            'action' => 'index',
            'items' => [
                [
                    'description' => 'Paged list of authors by name',
                    'method' => 'GET',
                    'route' => '/{lang}/author/search',
                    'params' => [
                        'lang' => 'path',
                        'name' => 'query',
                        'page' => 'query',
                        'maxResults' => 'query',
                    ],
                ],
                [
                    'description' => 'Create new author',
                    'method' => 'POST',
                    'route' => '/{lang}/author/create',
                    'params' => [
                        'lang' => 'path',
                        'name' => 'body',
                    ],
                ],
                [
                    'description' => 'Get author by id',
                    'method' => 'GET',
                    'route' => '/{lang}/author/{id}',
                    'params' => [
                        'lang' => 'path',
                        'id' => 'path',
                    ],
                ],
                [
                    'description' => 'Update author by id',
                    'method' => 'PATCH',
                    'route' => '/{lang}/author/{id}',
                    'params' => [
                        'lang' => 'path',
                        'id' => 'path',
                        'name' => 'body',
                    ],
                ],
                [
                    'description' => 'Delete author by id',
                    'method' => 'DELETE',
                    'route' => '/{lang}/author/{id}',
                    'params' => [
                        'lang' => 'path',
                        'id' => 'path',
                    ],
                ],
            ],
        ], Response::HTTP_OK);
    }
}
