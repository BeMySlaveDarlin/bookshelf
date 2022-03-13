<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository implements RepositoryWithHelpersInterface
{
    use HelpersTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function add(Book $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);

        $entity->mergeNewTranslations();

        if ($flush) {
            $this->_em->flush();
        }

        $this->_em->refresh($entity);
    }

    public function remove(Book $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function create(string $title, string $lang): Book
    {
        $book = new Book();
        $book->translate($lang)->setTitle($title);

        return $book;
    }

    public function findOneOrCreate(string $title, string $lang): Book
    {
        $book = $this->findOneByTitle($title, true);
        if (null === $book) {
            $book = $this->create($title, $lang);
        }

        return $book;
    }

    public function findFirst(): ?Book
    {
        return $this
            ->createQueryBuilder('book')
            ->orderBy('book.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneById(int $id)
    {
        $result = parent::findOneBy(['id' => $id]);
        if (null === $result) {
            throw new EntityNotFoundException(\sprintf('Entity with id %s not found', $id));
        }

        return $result;
    }

    /**
     * @return Book[] Returns an array of Book objects
     */
    public function findByTitle(string $search, int $offset = 1, int $maxResults = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('book');

        return $queryBuilder
            ->innerJoin(BookTranslation::class, 'book_translations')
            ->andWhere('book_translations.translatable = book.id')
            ->andWhere('book_translations.title LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('book.id', 'ASC')
            ->setFirstResult(($offset - 1) * $maxResults)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByTitle(string $search, bool $strict = false): ?Book
    {
        $queryBuilder = $this
            ->createQueryBuilder('book')
            ->innerJoin(BookTranslation::class, 'book_translations')
            ->andWhere('book_translations.translatable = book.id');

        if ($strict) {
            $queryBuilder
                ->andWhere('book_translations.title = :search')
                ->setParameter('search', $search);
        } else {
            $queryBuilder
                ->andWhere('book_translations.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
