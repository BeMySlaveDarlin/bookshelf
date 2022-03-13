<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\AuthorTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository implements RepositoryWithHelpersInterface
{
    use HelpersTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function add(Author $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);

        $entity->mergeNewTranslations();

        if ($flush) {
            $this->_em->flush();
        }

        $this->_em->refresh($entity);
    }

    public function remove(Author $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function create(string $name, string $lang): Author
    {
        $author = new Author();
        $author->translate($lang)->setName($name);

        return $author;
    }

    public function findOneOrCreate(string $name, string $lang): Author
    {
        $author = $this->findOneByName($name, true);
        if (null === $author) {
            $author = $this->create($name, $lang);
        }

        return $author;
    }

    public function findFirst(): ?Author
    {
        return $this
            ->createQueryBuilder('author')
            ->orderBy('author.id', 'ASC')
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
     * @return Author[] Returns an array of Author objects
     */
    public function findByName(string $search, int $offset = 1, int $maxResults = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('author');

        return $queryBuilder
            ->innerJoin(AuthorTranslation::class, 'author_translations')
            ->andWhere('author_translations.translatable = author.id')
            ->andWhere('author_translations.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('author.id', 'ASC')
            ->setFirstResult(($offset - 1) * $maxResults)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByName(string $search, bool $strict = false): ?Author
    {
        $queryBuilder = $this
            ->createQueryBuilder('author')
            ->innerJoin(AuthorTranslation::class, 'author_translations')
            ->andWhere('author_translations.translatable = author.id');

        if ($strict) {
            $queryBuilder
                ->andWhere('author_translations.name = :search')
                ->setParameter('search', $search);
        } else {
            $queryBuilder
                ->andWhere('author_translations.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
