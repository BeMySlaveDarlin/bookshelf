<?php

namespace App\DataFixture;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nubs\RandomNameGenerator\Alliteration;
use Nubs\RandomNameGenerator\Vgng;

class AppFixtures extends Fixture
{
    private AuthorRepository $authorRepository;
    private BookRepository $bookRepository;

    private const DEFAULT_LANGUAGE = 'en';

    private const START_OFFSET = 1;
    private const MAX_RECORDS = 10000;

    private const MIN_AUTHORS_PER_BOOK = 1;
    private const MAX_AUTHORS_PER_BOOK = 5;

    public function __construct(AuthorRepository $authorRepository, BookRepository $bookRepository)
    {
        $this->authorRepository = $authorRepository;
        $this->bookRepository = $bookRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $authors = [];

        $authorsGenerator = new Alliteration();
        for ($inc = self::START_OFFSET; $inc <= self::MAX_RECORDS; $inc++) {
            $author = $this->authorRepository->create($authorsGenerator->getName(), self::DEFAULT_LANGUAGE);
            $manager->persist($author);
            $author->mergeNewTranslations();

            $authors[$inc] = $author;
        }

        $booksGenerator = new Vgng();
        for ($inc = self::START_OFFSET; $inc <= self::MAX_RECORDS; $inc++) {
            $book = $this->bookRepository->create($booksGenerator->getName(), self::DEFAULT_LANGUAGE);
            $totalAuthors = \random_int(self::MIN_AUTHORS_PER_BOOK, self::MAX_AUTHORS_PER_BOOK);
            for ($totalAuthorsCount = self::START_OFFSET; $totalAuthorsCount < $totalAuthors; $totalAuthorsCount++) {
                $book->setAuthor($authors[\random_int(self::START_OFFSET, self::MAX_RECORDS)]);
            }

            $manager->persist($book);
            $book->mergeNewTranslations();
        }
        $manager->flush();
    }
}
