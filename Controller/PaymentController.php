<?php

namespace MiPago\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use MiPago\Bundle\Model\Payment;
use MiPago\Bundle\Services\MiPagoService;
use Psr\Log\LoggerInterface;

use Exception;

class PaymentController extends AbstractController
{
    public function __construct(private $forwardController)
    {
    }

    #[Route(path: '/sendRequest', name: 'mipago_sendRequest', methods: ['GET', 'POST'])]
    public function sendRequest(Request $request, MiPagoService $miPagoService, LoggerInterface $logger)
    {
        $logger->debug('-->sendRequest: Start');
        $locale = $this->__setLocale($request);
        $reference_number = str_pad((string) $request->get('reference_number'), 10, '0', STR_PAD_LEFT);
        $payment_limit_date = new \DateTime($request->get('payment_limit_date'));
        $quantity = $request->get('quantity');
        $suffix = $request->get('suffix');
        $extra = $request->get('extra');
        $sender = $request->get('sender');
        try {
            $result = $miPagoService->make_payment_request($reference_number, $payment_limit_date, $sender, $suffix, $quantity, $locale, $extra);
        } catch (Exception $e) {
            $logger->debug($e);
            $logger->debug('<--sendRequest: Exception: '.$e->getMessage());

            return $this->render('@MiPago/default/error.html.twig', [
                'exception' => $e,
                'suffixes' => implode(',', $miPagoService->getSuffixes()),
            ]);
        }
        $logger->debug('<--sendRequest: End OK');

        return $this->render('@MiPago/default/request.html.twig', $result);
    }

    #[Route(path: '/thanks', name: 'mipago_thanks', methods: ['GET'])]
    public function thanks(LoggerInterface $logger)
    {
        $logger->debug('-->thanks: Start');
        $logger->debug('<--thanks: End');

        return $this->render('@MiPago/default/thanks.html.twig');
    }

    #[Route(path: '/confirmation', name: 'mipago_confirmation', methods: ['POST'])]
    public function confirmation(Request $request, MiPagoService $miPagoService, LoggerInterface $logger)
    {
        $logger->debug('-->confirmation: Start');
        $logger->debug($request->getContent());
        $payment = $miPagoService->process_payment_confirmation($request->getContent());
        if (null != $this->forwardController) {
            $logger->debug('-->confirmation: End OK');

            return $this->forward($this->forwardController, [
                'payment' => $payment,
                ]);
        }
        $logger->debug('-->confirmation: End OK withJSONResponse');

        return new JsonResponse('OK');
    }

    private function __setLocale($request)
    {
        $locale = $request->attributes->get('_locale');
        if (null !== $locale) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $locale = $request->getSession()->get('_locale');
        }

        return $locale;
    }
}
