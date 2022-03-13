<?php

namespace App\Controller;

use App\Helper\TypeCaster;
use App\Repository\AuthorRepository;
use App\Response\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    private ResponseFactory $formatter;
    private AuthorRepository $authorRepository;

    public function __construct(ResponseFactory $responseFormatter, AuthorRepository $authorRepository)
    {
        $this->formatter = $responseFormatter;
        $this->authorRepository = $authorRepository;
    }

    /**
     * @Route("/{lang}/author/search", name="app_author_search", methods={"GET"})
     */
    public function search(string $lang, Request $request): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $authors = $this->authorRepository->findByName(
                TypeCaster::asString($request->query->get('name')),
                TypeCaster::asInt($request->query->get('page', 1)),
                TypeCaster::asInt($request->query->get('maxResults', 10)),
            );

            return $this->formatter->createSuccessResponse($this->authorRepository->toArray($authors));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/{lang}/author/create", name="app_author_create", methods={"POST"})
     */
    public function create(string $lang, Request $request): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $author = $this->authorRepository->create(
                TypeCaster::asString($request->request->get('name')),
                $lang
            );

            $this->authorRepository->add($author);

            return $this->formatter->createSuccessResponse($author->toArray($lang));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/{lang}/author/{id}", name="app_author_view", methods={"GET"})
     */
    public function view(string $lang, int $id): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $author = $this->authorRepository->findOneById($id);

            return $this->formatter->createSuccessResponse($author->toArray($lang));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/{lang}/author/{id}", name="app_author_update", methods={"PATCH"})
     */
    public function update(string $lang, int $id, Request $request): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $author = $this->authorRepository->findOneById($id);
            $author->translate($lang)->setName(
                TypeCaster::asString($request->request->get('name'))
            );

            $this->authorRepository->add($author);

            return $this->formatter->createSuccessResponse($author->toArray($lang));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/{lang}/author/{id}", name="app_author_delete", methods={"DELETE"})
     */
    public function delete(string $lang, int $id): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $author = $this->authorRepository->findOneById($id);

            $this->authorRepository->remove($author);

            return $this->formatter->createSuccessResponse([], \sprintf('Author %s deleted', $id));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
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
            'data' => [
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
