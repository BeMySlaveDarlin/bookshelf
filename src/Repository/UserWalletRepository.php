<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserWallet;
use App\Helper\CurrencyService;
use App\Helper\MoneyHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserWallet|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWallet|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWallet[]    findAll()
 * @method UserWallet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWalletRepository extends ServiceEntityRepository implements RepositoryWithHelpersInterface
{
    use HelpersTrait;

    private CurrencyService $currencyService;

    public function __construct(ManagerRegistry $registry, CurrencyService $currencyService)
    {
        parent::__construct($registry, UserWallet::class);

        $this->currencyService = $currencyService;
    }

    public function add(UserWallet $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(UserWallet $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function create(
        User $user,
        MoneyHelper $amount,
        string $currencyCode = CurrencyService::CURRENCY_RUB
    ): UserWallet {
        $userWallet = new UserWallet($this->currencyService);
        $userWallet->setUser($user);
        $userWallet->setAmount($amount);
        $userWallet->setCurrencyCode($currencyCode);

        return $userWallet;
    }

    public function findOneById(int $id): UserWallet
    {
        $result = parent::findOneBy(['id' => $id]);
        if (null === $result) {
            throw new EntityNotFoundException(\sprintf('Entity with id %s not found', $id));
        }

        return $result;
    }
}
