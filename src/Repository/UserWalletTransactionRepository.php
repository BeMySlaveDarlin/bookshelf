<?php

namespace App\Repository;

use App\Entity\UserWalletTransaction;
use App\Helper\PaymentService;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserWalletTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWalletTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWalletTransaction[]    findAll()
 * @method UserWalletTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWalletTransactionRepository extends ServiceEntityRepository implements RepositoryWithHelpersInterface
{
    use HelpersTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWalletTransaction::class);
    }

    public function add(UserWalletTransaction $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(UserWalletTransaction $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findSumByReasonAndDate(
        int $userWalletId,
        ?string $reason = null,
        ?DateTimeImmutable $dateFrom = null,
        ?DateTimeImmutable $dateTo = null
    ) {
        $queryBuilder = $this->createQueryBuilder('uwt')
            ->select("SUM(uwt.amount) as sum, uwt.type, uwt.reason")
            ->andWhere('uwt.userWallet = :userWalletId')
            ->setParameter('userWalletId', $userWalletId);

        if (null !== $reason && \in_array($reason, PaymentService::ALLOWED_TRANSACTION_REASONS)) {
            $queryBuilder
                ->andWhere('uwt.reason = :reason')
                ->setParameter('reason', $reason);
        }

        if (null !== $dateFrom) {
            $queryBuilder
                ->andWhere('uwt.createdAt >= :dateFrom')
                ->setParameter('dateFrom', $dateFrom->format(\DATE_ATOM));
        }

        if (null !== $dateTo) {
            $queryBuilder
                ->andWhere('uwt.createdAt <= :dateTo')
                ->setParameter('dateTo', $dateTo->format(\DATE_ATOM));
        }

        return $queryBuilder->groupBy("uwt.type, uwt.reason")->getQuery()->getResult();
    }
}
