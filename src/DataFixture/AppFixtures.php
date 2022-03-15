<?php

namespace App\DataFixture;

use App\Helper\CurrencyService;
use App\Helper\MoneyHelper;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Repository\UserWalletRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nubs\RandomNameGenerator\Alliteration;
use Nubs\RandomNameGenerator\Vgng;

class AppFixtures extends Fixture
{
    private AuthorRepository $authorRepository;
    private BookRepository $bookRepository;
    private UserRepository $userRepository;
    private UserWalletRepository $userWalletRepository;

    private const DEFAULT_LANGUAGE = 'en';

    private const START_OFFSET = 1;
    private const MAX_RECORDS = 10;

    private const MIN_AUTHORS_PER_BOOK = 1;
    private const MAX_AUTHORS_PER_BOOK = 5;

    public function __construct(
        AuthorRepository $authorRepository,
        BookRepository $bookRepository,
        UserRepository $userRepository,
        UserWalletRepository $userWalletRepository
    ) {
        $this->authorRepository = $authorRepository;
        $this->bookRepository = $bookRepository;
        $this->userRepository = $userRepository;
        $this->userWalletRepository = $userWalletRepository;
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

        for ($inc = self::START_OFFSET; $inc <= self::MAX_RECORDS; $inc++) {
            $user = $this->userRepository->create($authorsGenerator->getName());

            $currency = CurrencyService::ALLOWED_CURRENCIES[\array_rand(CurrencyService::ALLOWED_CURRENCIES)];
            $userWallet = $this->userWalletRepository->create($user, new MoneyHelper(0), $currency);

            $manager->persist($user);
            $manager->persist($userWallet);
        }

        $manager->flush();
    }
}
