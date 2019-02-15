<?php

namespace MiPago\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use MiPago\Bundle\Entity\Payment;
use MiPago\Bundle\Forms\PaymentTypeForm;
use MiPago\Bundle\Services\MiPagoService;
use Psr\Log\LoggerInterface;
use Exception;

class PaymentController extends Controller
{
    /**
     * @Route("/sendRequest", name="mipago_sendRequest", methods={"GET","POST"})
     */
    public function sendRequestAction(Request $request, MiPagoService $miPagoService, LoggerInterface $logger)
    {
	$logger->debug('-->sendRequestAction: Start');
	$locale = $this->__setLocale($request);
	$reference_number = str_pad($request->get('reference_number'), 10, "0", STR_PAD_LEFT);
	$payment_limit_date = new \DateTime($request->get('payment_limit_date'));
	$quantity = $request->get('quantity');
	$suffix = $request->get('suffix');
	$extra = $request->get('extra');
	try {
	    $result = $miPagoService->make_payment_request($reference_number, $payment_limit_date, $suffix, $quantity, $locale, $extra);
	} catch ( Exception $e ) {
	    $logger->debug($e);
	    $logger->debug('<--sendRequestAction: Exception: ' . $e->getMessage());
	    return $this->render('@MiPago/default/error.html.twig', [
		'exception' => $e,
		'suffixes' => implode(",", $this->getParameter('mipago.suffixes')),
	    ]);
	}
	$logger->debug('<--sendRequestAction: End OK');
	return $this->render('@MiPago/default/request.html.twig', $result );
    }

    /**
     * @Route("/thanks", name="mipago_thanks" , methods={"GET"})
     */
    public function thanksAction()
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
	$forwardController = $this->getParameter("mipago.forwardController");
	if ($forwardController != null ) {
	    $logger->debug('-->confirmationAction: End OK');
	    return $this->forward($forwardController,[
		'payment' => $payment
	    ]);
	} else {
	    $logger->debug('-->confirmationAction: End OK withJSONResponse');
	    return new JsonResponse ("OK");
	}
	    
//	return $this->render('@MiPago/default/confirmation.html.twig',[
//	    'payment' => $payment
//	]);
    }
    
    /**
     * @Route("/admin/payments", name="mipago_list_payments", methods={"GET","POST"})
     * #Security("has_role('ROLE_ADMIN')")
     */
    public function listPaymentsAction(Request $request, LoggerInterface $logger)
    {
	$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
	$logger->debug('-->listPaymentsAction: Start');
	$user = $this->get('security.token_storage')->getToken()->getUser();
	$roles = ($user === "anon.") ? ["IS_AUTHENTICATED_ANONYMOUSLY"] : $user->getRoles();	
	$locale = $this->__setLocale($request);
	$em = $this->getDoctrine()->getManager();
	$form = $this->createForm(PaymentTypeForm::class, null, [
	    'search' => true,
	    'readonly' => false,
	]);
	$form->handleRequest($request);
	if ( $form->isSubmitted() && $form->isValid() ) {
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
//	    'payments' => $results,
	]);

//	return $this->render('@MiPago/default/confirmation.html.twig',[
//	    'payment' => $payment
//	]);
    }

    /**
     * @Route("//admin/payment/{id}", name="mipago_show_payment")
     */

    public function showAction (Request $request, Payment $payment, LoggerInterface $logger ){
	$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
	$logger->debug('-->showAction: Start');
	$user = $this->get('security.token_storage')->getToken()->getUser();
	$logger->debug('Payment number: '.$payment->getId());
	$form = $this->createForm(PaymentTypeForm::class, $payment->toArray(), [
	    'search' => false,
	    'readonly' => true,
	]);
	return 	$this->render('@MiPago/default/show.html.twig', [
	    'form' => $form->createView(),
	    'payment' => $payment,
	    'readonly' => true,
	    'search' => false,
	]);
    }


    private function __setLocale($request) {
	$locale = $request->attributes->get('_locale');
	if ( $locale !== null ) {
	    $request->getSession()->set('_locale', $locale);
	} else {
	    $locale = $request->getSession()->get('_locale');
	}
	return $locale;
    }
}