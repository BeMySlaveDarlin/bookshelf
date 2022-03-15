<?php

namespace App\Controller;

use App\Helper\PaymentService;
use App\Helper\TypeCaster;
use App\Repository\UserWalletRepository;
use App\Repository\UserWalletTransactionRepository;
use App\Response\ResponseFactory;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class WalletController extends AbstractController
{
    private ResponseFactory $formatter;
    private PaymentService $paymentService;
    private UserWalletRepository $userWalletRepository;
    private UserWalletTransactionRepository $userWalletTransactionRepository;

    public function __construct(
        ResponseFactory $responseFormatter,
        PaymentService $paymentService,
        UserWalletRepository $userWalletRepository,
        UserWalletTransactionRepository $userWalletTransactionRepository
    ) {
        $this->formatter = $responseFormatter;
        $this->paymentService = $paymentService;
        $this->userWalletRepository = $userWalletRepository;
        $this->userWalletTransactionRepository = $userWalletTransactionRepository;
    }

    /**
     * @Route("/wallet/{id}", name="app_wallet_balance", methods={"GET"})
     */
    public function balance(int $id): JsonResponse
    {
        try {
            $wallet = $this->userWalletRepository->findOneById($id);

            return $this->formatter->createSuccessResponse($wallet->toArray());
        } catch (Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/wallet/{id}", name="app_wallet_create_transaction", methods={"POST"})
     */
    public function transaction(int $id, Request $request): JsonResponse
    {
        try {
            $wallet = $this->userWalletRepository->findOneById($id);
            $transaction = $this->paymentService->transact($wallet, $request);

            return $this->formatter->createSuccessResponse($transaction->toArray());
        } catch (Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/wallet/report/{id}", name="app_wallet_report", methods={"GET"})
     */
    public function report(int $id, Request $request): JsonResponse
    {
        try {
            $reason = TypeCaster::asNullableString($request->query->get('reason'));
            $from = TypeCaster::asNullableString($request->query->get('from'));
            $to = TypeCaster::asNullableString($request->query->get('to'));

            $dateFrom = null !== $from ? DateTimeImmutable::createFromFormat('Y-m-d', $from) : null;
            $dateTo = null !== $to ? DateTimeImmutable::createFromFormat('Y-m-d', $to) : null;

            $sum = $this->userWalletTransactionRepository->findSumByReasonAndDate($id, $reason, $dateFrom, $dateTo);

            return $this->formatter->createSuccessResponse([$sum]);
        } catch (Throwable $throwable) {
            return $this->formatter->createErrorResponse($throwable);
        }
    }

    /**
     * @Route("/wallet", name="app_wallet_index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'status' => Response::HTTP_OK,
            'action' => 'index',
            'items' => [
                [
                    'description' => 'Check out wallet balance',
                    'method' => 'GET',
                    'route' => '/wallet/{id}',
                    'params' => [
                        'id' => 'path',
                    ],
                ],
                [
                    'description' => 'Create new transaction',
                    'method' => 'POST',
                    'route' => '/wallet/{id}',
                    'params' => [
                        'id' => 'path',
                        'amount' => 'body',
                        'currency' => 'body',
                        'type' => 'body',
                        'reason' => 'body',
                    ],
                ],
                [
                    'description' => 'Get sum amount of given reason for date range',
                    'method' => 'GET',
                    'route' => '/wallet/report/{id}',
                    'params' => [
                        'id' => 'path',
                        'reason' => 'query',
                        'from' => 'query',
                        'to' => 'query',
                    ],
                ],
            ],
        ], Response::HTTP_OK);
    }
}
