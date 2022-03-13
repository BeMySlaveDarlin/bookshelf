<?php

namespace App\Controller;

use App\Helper\TypeCaster;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Response\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    private ResponseFactory $formatter;
    private AuthorRepository $authorRepository;
    private BookRepository $bookRepository;

    public function __construct(
        ResponseFactory $responseFormatter,
        AuthorRepository $authorRepository,
        BookRepository $bookRepository
    ) {
        $this->formatter = $responseFormatter;
        $this->authorRepository = $authorRepository;
        $this->bookRepository = $bookRepository;
    }

    /**
     * @Route("/{lang}/book/search", name="app_book_search", methods={"GET"})
     */
    public function search(string $lang, Request $request): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $books = $this->bookRepository->findByTitle(
                TypeCaster::asString($request->query->get('title')),
                TypeCaster::asInt($request->query->get('page', 1)),
                TypeCaster::asInt($request->query->get('maxResults', 10)),
            );

            return $this->formatter->createSuccessResponse($this->bookRepository->toArray($books));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/{lang}/book/create", name="app_book_create", methods={"POST"})
     */
    public function create(string $lang, Request $request): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $book = $this->bookRepository->create(
                TypeCaster::asString($request->request->get('title')),
                $lang
            );
            $author = $this->authorRepository->findOneOrCreate(
                TypeCaster::asString($request->request->get('author')),
                $lang
            );
            $book->setAuthor($author);

            $this->authorRepository->add($author, false);
            $this->bookRepository->add($book, false);

            $this->bookRepository->flush();

            return $this->formatter->createSuccessResponse($book->toArray($lang));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/{lang}/book/{id}", name="app_book_view", methods={"GET"})
     */
    public function view(string $lang, int $id): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $book = $this->bookRepository->findOneById($id);

            return $this->formatter->createSuccessResponse($book->toArray($lang));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/{lang}/book/{id}", name="app_book_update", methods={"PATCH"})
     */
    public function update(string $lang, int $id, Request $request): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $book = $this->bookRepository->findOneById($id);
            $book->translate($lang)->setTitle(TypeCaster::asString($request->request->get('title')));

            $this->bookRepository->add($book);

            return $this->formatter->createSuccessResponse($book->toArray($lang));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/{lang}/book/{id}", name="app_book_delete", methods={"DELETE"})
     */
    public function delete(string $lang, int $id): JsonResponse
    {
        $this->formatter->setLang($lang);
        try {
            $book = $this->bookRepository->findOneById($id);

            $this->bookRepository->remove($book);

            return $this->formatter->createSuccessResponse([], \sprintf('Book %s deleted', $id));
        } catch (\Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/{lang}/book", name="app_book_index", methods={"GET"})
     */
    public function index(string $lang): JsonResponse
    {
        return new JsonResponse([
            'lang' => $lang,
            'status' => Response::HTTP_OK,
            'action' => 'index',
            'items' => [
                [
                    'description' => 'Paged list of books by title',
                    'method' => 'GET',
                    'route' => '/{lang}/book/search',
                    'params' => [
                        'lang' => 'path',
                        'title' => 'query',
                        'page' => 'query',
                        'maxResults' => 'query',
                    ],
                ],
                [
                    'description' => 'Create new book',
                    'method' => 'POST',
                    'route' => '/{lang}/book/create',
                    'params' => [
                        'lang' => 'path',
                        'title' => 'body',
                        'author' => 'body',
                    ],
                ],
                [
                    'description' => 'Get book by id',
                    'method' => 'GET',
                    'route' => '/{lang}/book/{id}',
                    'params' => [
                        'lang' => 'path',
                        'id' => 'path',
                    ],
                ],
                [
                    'description' => 'Update book by id',
                    'method' => 'PATCH',
                    'route' => '/{lang}/book/{id}',
                    'params' => [
                        'lang' => 'path',
                        'id' => 'path',
                        'title' => 'body',
                    ],
                ],
                [
                    'description' => 'Delete book by id',
                    'method' => 'DELETE',
                    'route' => '/{lang}/book/{id}',
                    'params' => [
                        'lang' => 'path',
                        'id' => 'path',
                    ],
                ],
            ],
        ], Response::HTTP_OK);
    }
}
