<?php

namespace MiPago\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use MiPago\Bundle\Entity\Payment;
use MiPago\Bundle\Forms\PaymentTypeForm;
use MiPago\Bundle\Services\MiPagoService;
use Psr\Log\LoggerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PaymentController extends AbstractController
{
    private $forwardController;

    public function __construct($forwardController)
    {
        $this->forwardController = $forwardController;
    }

    /**
     * @Route("/sendRequest", name="mipago_sendRequest", methods={"GET","POST"})
     */
    public function sendRequestAction(Request $request, MiPagoService $miPagoService, LoggerInterface $logger)
    {
        $logger->debug('-->sendRequestAction: Start');
        $locale = $this->__setLocale($request);
        $reference_number = str_pad($request->get('reference_number'), 10, '0', STR_PAD_LEFT);
        $payment_limit_date = new \DateTime($request->get('payment_limit_date'));
        $quantity = $request->get('quantity');
        $suffix = $request->get('suffix');
        $extra = $request->get('extra');
        $sender = $request->get('sender');
        try {
            $result = $miPagoService->make_payment_request($reference_number, $payment_limit_date, $sender, $suffix, $quantity, $locale, $extra);
        } catch (Exception $e) {
            $logger->debug($e);
            $logger->debug('<--sendRequestAction: Exception: '.$e->getMessage());

            return $this->render('@MiPago/default/error.html.twig', [
                'exception' => $e,
                'suffixes' => implode(',', $miPagoService->getSuffixes()),
            ]);
        }
        $logger->debug('<--sendRequestAction: End OK');

        return $this->render('@MiPago/default/request.html.twig', $result);
    }

    /**
     * @Route("/thanks", name="mipago_thanks" , methods={"GET"})
     */
    public function thanksAction(LoggerInterface $logger)
    {
        $logger->debug('-->thanksAction: Start');
        $logger->debug('<--thanksAction: End');

        return $this->render('@MiPago/default/thanks.html.twig');
    }

    /**
     * @Route("/confirmation", name="mipago_confirmation", methods={"POST"})
     */
    public function confirmationAction(Request $request, MiPagoService $miPagoService, LoggerInterface $logger)
    {
        $logger->debug('-->confirmationAction: Start');
        $logger->debug($request->getContent());
        $payment = $miPagoService->process_payment_confirmation($request->getContent());
        if (null != $this->forwardController) {
            $logger->debug('-->confirmationAction: End OK');

            return $this->forward($this->forwardController, [
                'payment' => $payment,
                ]);
        }
        $logger->debug('-->confirmationAction: End OK withJSONResponse');

        return new JsonResponse('OK');
    }

    /**
     * @Route("/admin/payments", name="mipago_list_payments", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function listPaymentsAction(Request $request, LoggerInterface $logger)
    {
        $logger->debug('-->listPaymentsAction: Start');
        $this->__setLocale($request);
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PaymentTypeForm::class, null, [
            'search' => true,
            'readonly' => false,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $results = $em->getRepository(Payment::class)->findPaymentsBy($data);

            return $this->render('@MiPago/default/list.html.twig', [
                'form' => $form->createView(),
                'payments' => $results,
                'search' => true,
                'readonly' => false,
            ]);
        }
        $logger->debug('<--listPaymentsAction: End OK');

        return $this->render('@MiPago/default/list.html.twig', [
            'form' => $form->createView(),
            'search' => true,
            'readonly' => false,
        ]);
    }

    /**
     * @Route("/admin/payment/{id}", name="mipago_show_payment")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showAction(Request $request, Payment $payment, LoggerInterface $logger)
    {
        $logger->debug('-->showAction: Start');
        $logger->debug('Payment number: '.$payment->getId());
        $form = $this->createForm(PaymentTypeForm::class, $payment->toArray(), [
            'search' => false,
            'readonly' => true,
        ]);

        return    $this->render('@MiPago/default/show.html.twig', [
            'form' => $form->createView(),
            'payment' => $payment,
            'readonly' => true,
            'search' => false,
        ]);
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
