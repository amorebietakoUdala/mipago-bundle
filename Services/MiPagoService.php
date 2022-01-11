<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MiPago\Bundle\Services;

use Symfony\Component\DomCrawler\Crawler;
use MiPago\Bundle\Entity\Payment;
use Exception;

/**
 * Description of MiPagoConstants.
 *
 * @author ibilbao
 */
class MiPagoService
{
    const TEST_ENVIRON_INITIALIZATION_URL = 'https://mipago.sandbox.euskadi.eus/p12gWar/p12gRPCDispatcherServlet';

    const TEST_ENVIRON_SERVICE_URL = 'https://mipago.sandbox.euskadi.eus/p12iWar/p12iRPCDispatcherServlet';

    const PROD_ENVIRON_INITIALIZATION_URL = 'https://www.euskadi.eus/p12gWar/p12gRPCDispatcherServlet';

    const PROD_ENVIRON_SERVICE_URL = 'https://www.euskadi.eus/y22-pay/es/p12uiPaymentWar/p12uiRPCDispatcherServlet';
    #const PROD_ENVIRON_SERVICE_URL = 'https://www.euskadi.eus/p12iWar/p12iRPCDispatcherServlet';

    /**
     * PREINICIALIZAR = "00";.
     */
    const PAYMENT_STATUS_NOT_INITIALIZED = '00';

    const PAYMENT_STATUS_INITIALIZED = '01';

    const PAYMENT_STATUS_OK = '04';

    const PAYMENT_STATUS_NOK = '05';

    const PAYMENT_STATUS_MESSAGES = [
        self::PAYMENT_STATUS_NOT_INITIALIZED => 'Payment could not be initialized. Probably incorrect suffix',
        self::PAYMENT_STATUS_INITIALIZED => 'Payment initialized. Sended to MiPago.',
        self::PAYMENT_STATUS_OK => 'Payment paid succesfully.',
        self::PAYMENT_STATUS_NOK => 'The was an error during payment.',
    ];

    public $template = null;

    public $MESSAGE_1_TEMPLATE = <<<M1T
    <mensaje id="1">
	<texto>
	    <eu>{eu}</eu>
	    <es>{es}</es>
	</texto>
    </mensaje>    
M1T;

    public $MESSAGE_2_TEMPLATE = <<<M1T
    <mensaje id="2">
	<texto>
	    <eu>{eu}</eu>
	    <es>{es}</es>
	</texto>
    </mensaje>    
M1T;

    public $MESSAGE_3_TEMPLATE = <<<M1T
    <mensaje id="3">
	<texto>
	    <eu>{eu}</eu>
	    <es>{es}</es>
	</texto>
    </mensaje>    
M1T;

    public $MESSAGE_4_TEMPLATE = <<<M1T
    <mensaje id="4">
	<texto>
	    <eu>{eu}</eu>
	    <es>{es}</es>
	</texto>
    </mensaje>    
M1T;

    public $LOGO_1_TEMPLATE = <<<XML
    <imagen id="logo1">
        <url><![CDATA[{url}]]></url>
    </imagen>
XML;

    public $LOGO_2_TEMPLATE = <<<XML
    <imagen id="logo2">
        <url><![CDATA[{url}]]></url>
    </imagen>
XML;

    public $LOGO_WRAPPER_TEMPLATE = <<<XML
    <imagenes>
	{data}
    </imagenes>
XML;

    public $MESSAGE_PAYMENT_TITLE = <<<XML
    <descripcion>
	<eu>{eu}</eu>
	<es>{es}</es>
    </descripcion>
XML;

    public $MESSAGE_PAYMENT_DESCRIPTION = <<<XML
    <descripcion>
	<eu>{eu}</eu>
	<es>{es}</es>
    </descripcion>
XML;

    public $PRESENTATION_XML = <<<XML
    <presentationRequestData>
	<idioma>{language}</idioma>
	<paymentModes>
	    {payment_mode}
	</paymentModes>
    </presentationRequestData>
XML;

    public $PROTOCOL_DATA_XML = <<<XML
    <protocolData>
	<urls>
	    <url id='urlVuelta'><![CDATA[{return_url}]]></url>
	</urls>
    </protocolData>
XML;

    private $pm = null;
    private $cpr = null;
    private $sender = null;
    private $format = null;
    private $language = null;
    private $return_url = null;
    private $payment_modes = [];
    private $test_environment = null;
    private $logger = null;
    private $suffixes = [];

    /**
     * @param PaymentManager  $em
     * @param string          $cpr
     * @param string          $sender
     * @param string          $format
     * @param array           $suffixes
     * @param string          $language
     * @param string          $return_url
     * @param bool            $test_environment
     * @param LoggerInterface $logger
     */
    public function __construct(\MiPago\Bundle\Doctrine\PaymentManager $pm, $cpr, $sender, $format, $suffixes, $language, $return_url, $test_environment, $payment_modes, $logger)
    {
        $this->pm = $pm;
        $this->cpr = $cpr;
        $this->sender = $sender;
        $this->format = $format;
        $this->suffixes = $suffixes;
        $this->language = $language;
        $this->return_url = $return_url;
        $this->payment_modes = $payment_modes;
        $this->test_environment = $test_environment;
        $this->logger = $logger;
        $this->template = file_get_contents(__DIR__ . '/../Resources/config/template.xml');
    }

    /**
     * This method creates an XML file and creates a payment request on the
     * Government platform in order to have the basis to be shown to the end
     * user.
     *
     * According to the payment platform specs, after the registration, an HTML
     * file is created which must be shown to the user. This HTML file has an
     * "auto-refresh" feature which allows to redirect the user to the payment
     * platform, where all the data of the payment is already entered.
     *
     * There, the enduser only has to select the bank of his choice to complete
     * the payment.
     * After completing the payment the user will be redirected to the
     * `return_url`.
     * See the documentation for more information about the parameters
     *
     * @param string    $cpr
     * @param string    $sender
     * @param string    $format
     * @param string    $suffix
     * @param string    $reference_number
     * @param \DateTime $payment_limit_date
     * @param float     $quantity
     * @param string    $language
     * @param string    $return_url
     * @param array     $payment_modes
     * @param bool      $test_environment
     * @param array     $extra
     *
     * @return string
     *
     * @throws Exception
     */
    public function make_payment_request(
        $reference_number,
        $payment_limit_date,
        $sender,
        $suffix,
        $quantity,
        $language,
        $extra
    ) {
        $pm = $this->pm;
        $cpr = $this->cpr;
        /* If sender is especified default is overwritten else takes the sender from the configuration file */
        if (null != $sender) {
            $this->sender = $sender;
        }
        $format = $this->format;
        //	$language = $this->language;
        $return_url = $this->return_url;
        $payment_modes = $this->payment_modes;
        $test_environment = $this->test_environment;
        $suffixes = $this->suffixes;

        if ($test_environment) {
            $INITIALIZATION_URL = $this::TEST_ENVIRON_INITIALIZATION_URL;
            $SERVICE_URL = $this::TEST_ENVIRON_SERVICE_URL;
        } else {
            $INITIALIZATION_URL = $this::PROD_ENVIRON_INITIALIZATION_URL;
            $SERVICE_URL = $this::PROD_ENVIRON_SERVICE_URL;
        }

        if ('9052180' != $cpr) {
            throw new Exception('We only accept payments with CPR 9052180');
        }

        if ('521' != $format) {
            throw new Exception('We only accept payments with Format 521');
        }

        $suffixesArray = [];
        if (0 !== strlen($suffixes[0])) {
            $suffixesArray = explode(',', $suffixes[0]);
        }
        $payment_modesArray = [];
        if (0 !== strlen($payment_modes[0])) {
            $payment_modesArray = explode(',', $payment_modes[0]);
        }
        if (count($suffixesArray) > 0 && !in_array($suffix, $suffixesArray)) {
            throw new Exception('Suffix, not allowed. The allowed suffixes are: %suffixes%');
        }

        // TODO Control the sender not found exception.

        $logger = $this->logger;
        $result = $this->__initialize_payment($reference_number, $payment_limit_date, $suffix, $quantity, $extra);

        $logger->debug($result);
        $result_fields = $this->__parse_initialization_response($result);

        if (self::PAYMENT_STATUS_NOK == $result_fields['payment_status']) {
            $error_code = array_key_exists('error_code', $result_fields) ? $result_fields['error_code'] : null;
            if ('pago_pagado' == $error_code) {
                $result_fields['payment_status'] = self::PAYMENT_STATUS_OK;
                $payment = $pm->getRepository()->findOneBy(['registeredPaymentId' => $result_fields['payment_id']]);
                $result_fields['payment'] = $payment;
            }
            throw new Exception('Already payd');
        }

        $registered_payment_id = $result_fields['payment_id'];
        if (null != $registered_payment_id) {
            $payment_modes_string = '';
            foreach ($payment_modesArray as $payment_mode) {
                $payment_modes_string .= str_replace('{}', $payment_mode, "<paymentMode oid='{}'/>");
            }
            $params = [
                '{language}' => $language,
                '{payment_mode}' => $payment_modes_string,
            ];
            $presentation_request_data = str_replace(array_keys($params), $params, $this->PRESENTATION_XML);
            $protocol_data = str_replace('{return_url}', $return_url, $this->PROTOCOL_DATA_XML);

            $payment = $pm->getRepository()->findOneBy(['registeredPaymentId' => $registered_payment_id]);
            if (null == $payment) {
                $payment = $pm->newPayment();
                $payment->setReferenceNumber($reference_number);
                $payment->setSuffix($suffix);
                $payment->setQuantity($quantity);
                $payment->setTimestamp(null);
                $payment->setRegisteredPaymentId($registered_payment_id);
                $payment->setName(array_key_exists('citizen_name', $extra) ? $extra['citizen_name'] : null);
                $payment->setSurname1(array_key_exists('citizen_surname_1', $extra) ? $extra['citizen_surname_1'] : null);
                $payment->setSurname2(array_key_exists('citizen_surname_2', $extra) ? $extra['citizen_surname_2'] : null);
                $payment->setCity(array_key_exists('citizen_city', $extra) ? $extra['citizen_city'] : null);
                $payment->setNif(array_key_exists('citizen_nif', $extra) ? $extra['citizen_nif'] : null);
                $payment->setAddress(array_key_exists('citizen_address', $extra) ? $extra['citizen_address'] : null);
                $payment->setPostalCode(array_key_exists('citizen_postal_code', $extra) ? $extra['citizen_postal_code'] : null);
                $payment->setTerritory(array_key_exists('citizen_territory', $extra) ? $extra['citizen_territory'] : null);
                $payment->setCountry(array_key_exists('citizen_country', $extra) ? $extra['citizen_country'] : null);
                $payment->setPhone(array_key_exists('citizen_phone', $extra) ? $extra['citizen_phone'] : null);
                $payment->setEmail(array_key_exists('citizen_email', $extra) ? $extra['citizen_email'] : null);
                $payment->setStatus(self::PAYMENT_STATUS_INITIALIZED);
                $pm->savePayment($payment);
            }
            $result = [
                'payment_status' => self::PAYMENT_STATUS_INITIALIZED,
                'payment' => $payment,
                'p12OidsPago' => $registered_payment_id,
                'p12iPresentationRequestData' => $presentation_request_data,
                'p12iProtocolData' => $protocol_data,
                'registered_payment_id' => $registered_payment_id,
                'serviceURL' => $SERVICE_URL,
            ];

            return $result;
        }
    }

    /**
     * Initializes Payments on MiPago platform.
     *  - reference_number: 10 digit string identifying the payment
     *  - payment_limit_date: \DateTime that indicates the last day to pay the receipt.
     *  - The suffix passed as parameter as a 3 digit string: '521'
     *  - quantity: The ammount to be payed in Euros.
     * - extra: Extra parameters of the payment name, surname, and so on.

     *
     * @param string    $reference_number
     * @param \DateTime $payment_limit_date
     * @param string    $suffix
     * @param float     $quantity
     * @param array     $extra
     */
    private function __initialize_payment($reference_number, $payment_limit_date, $suffix, $quantity, $extra)
    {
        $cpr = $this->cpr;
        $sender = $this->sender;
        $format = $this->format;
        $test_environment = $this->test_environment;

        if ($test_environment) {
            $INITIALIZATION_URL = $this::TEST_ENVIRON_INITIALIZATION_URL;
            $SERVICE_URL = $this::TEST_ENVIRON_SERVICE_URL;
        } else {
            $INITIALIZATION_URL = $this::PROD_ENVIRON_INITIALIZATION_URL;
            $SERVICE_URL = $this::PROD_ENVIRON_SERVICE_URL;
        }

        $payment_identification = $this->__calculate_payment_identification_notebook_c60(
            $payment_limit_date,
            $suffix
        );

        $quantity_string = str_pad($quantity * 100, 8, '0', STR_PAD_LEFT);

        $reference_number_with_control_digits = $this->__calculate_reference_number_with_control_digits_notebook_60(
            $sender,
            $reference_number,
            $payment_identification,
            $quantity_string
        );

        $payment_code = $this->__build_payment_code_notebook_60(
            $sender,
            $reference_number_with_control_digits,
            $payment_identification,
            $quantity_string
        );

        $message_1 = '';
        if (array_key_exists('message_1', $extra)) {
            $message_1 = str_replace(['{es}', '{eu}'], [$extra['message_1']['es'], $extra['message_1']['eu']], $this->MESSAGE_1_TEMPLATE);
        }

        $message_2 = '';
        if (array_key_exists('message_2', $extra)) {
            $message_2 = str_replace(['{es}', '{eu}'], [$extra['message_2']['es'], $extra['message_2']['eu']], $this->MESSAGE_2_TEMPLATE);
        }

        $message_3 = '';
        if (array_key_exists('message_3', $extra)) {
            $message_3 = str_replace(['{es}', '{eu}'], [$extra['message_3']['es'], $extra['message_3']['eu']], $this->MESSAGE_3_TEMPLATE);
        }

        $message_4 = '';
        if (array_key_exists('message_4', $extra)) {
            $message_4 = str_replace(['{es}', '{eu}'], [$extra['message_4']['es'], $extra['message_4']['eu']], $this->MESSAGE_4_TEMPLATE);
        }

        $message_payment_title = '';
        if (array_key_exists('message_payment_title', $extra)) {
            $message_payment_title = str_replace(['{es}', '{eu}'], [$extra['message_payment_title']['es'], $extra['message_payment_title']['eu']], $this->MESSAGE_PAYMENT_TITLE);
        }

        $mipago_payment_description = '';
        if (array_key_exists('mipago_payment_description', $extra)) {
            $mipago_payment_description = str_replace(['{es}', '{eu}'], [$extra['mipago_payment_description']['es'], $extra['mipago_payment_description']['eu']], $this->MESSAGE_PAYMENT_DESCRIPTION);
        }

        $logo_urls = '';
        if (array_key_exists('logo_1_url', $extra)) {
            $logo_urls .= str_replace('{logo_1_url}', $extra['logo_1_url'], $this->LOGO_1_TEMPLATE);
        }

        if (array_key_exists('logo_2_url', $extra)) {
            $logo_urls .= str_replace('{logo_2_url}', $extra['logo_2_url'], $this->LOGO_2_TEMPLATE);
        }

        if (array_key_exists('logo_urls', $extra)) {
            $logo_urls .= str_replace('{data}', $logo_urls, $this->LOGO_WRAPPER_TEMPLATE);
        }

        $pdf_xslt_url = '';
        if (array_key_exists('pdf_xslt_url', $extra)) {
            $pdf_xslt_url = str_replace(['{pdf_xslt_url}'], [$extra['pdf_xslt_url']], $this->PDF_XSLT_TEMPLATE);
        }

        $params = [
            '{code}' => $payment_code,
            '{cpr}' => $cpr,
            '{suffix}' => $suffix,
            '{quantity}' => $quantity_string,
            '{payment_identification}' => $payment_identification,
            '{end_date}' => $payment_limit_date->format('dmY'),
            '{format}' => $format,
            '{sender}' => $sender,
            '{reference_with_control}' => $reference_number_with_control_digits,
            '{reference}' => $reference_number,
            '{message_1}' => $message_1,
            '{message_2}' => $message_2,
            '{message_3}' => $message_3,
            '{message_4}' => $message_4,
            '{message_payment_title}' => $message_payment_title,
            '{mipago_payment_description}' => $mipago_payment_description,
            '{citizen_name}' => array_key_exists('citizen_name', $extra) ? $extra['citizen_name'] : '',
            '{citizen_surname_1}' => array_key_exists('citizen_surname_1', $extra) ? $extra['citizen_surname_1'] : '',
            '{citizen_surname_2}' => array_key_exists('citizen_surname_2', $extra) ? $extra['citizen_surname_2'] : '',
            '{citizen_city}' => array_key_exists('citizen_city', $extra) ? $extra['citizen_city'] : '',
            '{citizen_nif}' => array_key_exists('citizen_nif', $extra) ? $extra['citizen_nif'] : '',
            '{citizen_address}' => array_key_exists('citizen_address', $extra) ? $extra['citizen_address'] : '',
            '{citizen_postal_code}' => array_key_exists('citizen_postal_code', $extra) ? $extra['citizen_postal_code'] : '',
            '{citizen_territory}' => array_key_exists('citizen_territory', $extra) ? $extra['citizen_territory'] : '',
            '{citizen_country}' => array_key_exists('citizen_country', $extra) ? $extra['citizen_country'] : '',
            '{citizen_phone}' => array_key_exists('citizen_phone', $extra) ? $extra['citizen_phone'] : '',
            '{citizen_email}' => array_key_exists('citizen_email', $extra) ? $extra['citizen_email'] : '',
            '{logo_urls}' => array_key_exists('logo_urls', $extra) ? $extra['logo_urls'] : '',
            '{pdf_xslt_url}' => array_key_exists('pdf_xslt_url', $extra) ? $extra['pdf_xslt_url'] : '',
        ];
        $initialization_xml = str_replace(array_keys($params), $params, $this->template);

        $url = $INITIALIZATION_URL;
        $data = $initialization_xml;
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n"
                    . 'Content-Length: ' . strlen('xmlRPC=' . trim($data)) . "\r\n",
                'content' => 'xmlRPC=' . trim($data),
            ),
            "ssl"=>array(
                "allow_self_signed"=>true,
                "verify_peer"=>false,
                "verify_peer_name"=>false,
             ),
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }

    /**
     * Payment identification is calculated concatenating 5 values:
     *  - A constant: '1'
     *  - The suffix passed as parameter as a 3 digit string: '521'
     *  - The last 2 digits of the year of the date passed
     *      as parameter: '18'
     *  - The last digit of the year of the date passed
     *      as parameter: '8'
     *  - The ordinal day of the date passed as paramenter as a
     *      3 digit string: '521'.
     *
     * @param \DateTime $limit_date
     * @param string    $suffix
     */
    private function __calculate_payment_identification_notebook_c60(\DateTime $limit_date, $suffix)
    {
        $period = '1';
        $year_two_digits = $limit_date->format('y');
        $year_last_digit = substr($year_two_digits, -1);
        $year_ordinal_day = $limit_date->format('z') + 1;
        $pi = '{period}{suffix}{year_two_digits}{year_last_digit}{year_ordinal_day}';
        $params = [
            '{period}' => $period,
            '{suffix}' => str_pad($suffix, 3, '0', STR_PAD_RIGHT),
            '{year_two_digits}' => $year_two_digits,
            '{year_last_digit}' => $year_last_digit,
            '{year_ordinal_day}' => str_pad($year_ordinal_day, 3, '0', STR_PAD_LEFT),
        ];

        return str_replace(array_keys($params), $params, $pi);
    }

    /**
     * Control digits for the reference number are calculated as follows:
     *   - a: Multiply the sender value converted to an integer value by 76
     *   - b: Multiply the reference value converted to an integer value by 9
     *   - c: Sum the payment_identification converted to an integer
     *       value and the quantity value converted to an integer value
     *       and deduct 1.
     *   - d: Multiply the c value by 55.
     *   - e: sum a, b and d
     *   - Divide e by 97 and take the decimal values.
     *   - f: take the first 2 decimal values (add a 0 as a second digit if
     *       the division result creates just one decimal)
     *   - g: deduct f from 99.
     *   - Concatenate the reference number and g and create a 12 digit value.
     *
     * @param string $sender
     * @param string $reference_number
     * @param string $payment_identification
     * @param string $quantity
     */
    private function __calculate_reference_number_with_control_digits_notebook_60(
        $sender,
        $reference_number,
        $payment_identification,
        $quantity
    ) {
        if (10 != strlen($reference_number)) {
            throw new Exception('Invalid Reference Number');
        }
        $total = intval($sender) * 76;
        $total += intval($reference_number) * 9;
        $total += (intval($payment_identification) - 1 + intval($quantity)) * 55;

        $division_result = $total / 97.0;
        $decimals = explode('.', (string) $division_result);
        $first_two_decimals = substr($decimals[1], 0, 2);
        $control_digits = 99 - intval($first_two_decimals);
        $rncd = $reference_number . str_pad($control_digits, 2, '0', STR_PAD_LEFT);

        return $rncd;
    }

    /**
     * Payment code is calculated concatenating 6 values:
     * - A constant that represents this payment mode: '90521'
     * - A 6 digit sender code: '123456'
     * - A 12 digit reference number: '123456789012'
     * - A 10 digit payment identification number: '1234567890'
     * - A 8 digit value representing the number of euro cents to
     *     be payed: '0000001000'
     * - A constant value: '0'.
     *
     * @param string $sender
     * @param string $reference_number
     * @param string $payment_identification
     * @param string $quantity
     */
    private function __build_payment_code_notebook_60($sender, $reference_number, $payment_identification, $quantity)
    {
        $payment_code = '90521{sender}{reference_number}{payment_identification}{quantity}0';
        $params = [
            '{sender}' => str_pad($sender, 6, '0', STR_PAD_RIGHT),
            '{reference_number}' => str_pad($reference_number, 12, '0', STR_PAD_RIGHT),
            '{payment_identification}' => str_pad($payment_identification, 10, '0', STR_PAD_RIGHT),
            '{quantity}' => str_pad($quantity, 8, '0', STR_PAD_RIGHT),
        ];

        return str_replace(array_keys($params), $params, $payment_code);
    }

    /**
     * Parses payment initialization response XML and converts it an asociative array.
     * Set the status to PAY_STATUS_NOK ok when a validation message exists.
     * If no error it returns PAY_STATUS_INITIALIZED.
     *
     * @param string $xmlresponse
     *
     * @return array
     */
    private function __parse_initialization_response($xmlresponse)
    {
        $root = new Crawler($xmlresponse);
        $peticion = $root->filterXPath('.//peticionPago');
        $fields = [];
        $pago_id = null;
        if ($peticion->count() > 0) {
            $pago_id = $peticion->attr('id');
            if ($peticion->filterXPath('.//validacion')->count() > 0) {
                $error_code = $peticion->filterXPath('.//codigoError');
                $validation_message = $peticion->filterXPath('.//mensajeValidacion');
                $fields['error_code'] = ($error_code->count() > 0) ? $error_code->text() : null;
                $fields['message'] = ($validation_message->count() > 0) ? $validation_message->text() : null;
                $fields['payment_status'] = self::PAYMENT_STATUS_NOK;
            } else {
                $fields['payment_status'] = self::PAYMENT_STATUS_INITIALIZED;
            }
            $fields['payment_id'] = $pago_id;
        } else {
            $fields['payment_status'] = self::PAYMENT_STATUS_NOT_INITIALIZED;
        }

        return $fields;
    }

    /**
     * Parses payment confirmation response XML and converts it an asociative array of the relevant values.
     *
     * @param string $xmlresponse
     *
     * @return array
     */
    private function __parse_confirmation_response($xmlresponse)
    {
        $errores = [];
        $xml = simplexml_load_string($xmlresponse, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);

        if (null !== $xml->estado->mensajes->mensaje) {
            foreach ($xml->estado->mensajes->mensaje as $mensaje) {
                $errores[] = trim($mensaje->texto->es);
            }
        }
        if (count($errores) > 0) {
            $text_message = '';
            foreach ($errores as $error) {
                $text_message = $text_message . $error . '<br>';
            }
        } else {
            $text_message = null;
        }
        $root = new Crawler($xmlresponse);
        $fields = [
            'id' => ($root->filterXPath('.//id')->count() > 0) ? $root->filterXPath('.//id')->text() : null,
            'payment_id' => ($root->filterXPath('.//paymentid')->count() > 0) ? $root->filterXPath('.//paymentid')->text() : null,
            'referenceNumberDC' => ($root->filterXPath('.//referencia')->count() > 0) ? $root->filterXPath('.//referencia')->text() : null,
            'codigo' => ($root->filterXPath('.//estado/codigo')->count() > 0) ? $root->filterXPath('.//estado/codigo')->text() : null,
            'quantity' => ($root->filterXPath('.//importe')->count() > 0) ? $root->filterXPath('.//importe')->text() : null,
            'operationNumber' => ($root->filterXPath('.//numerooperacion')->count() > 0) ? $root->filterXPath('.//numerooperacion')->text() : null,
            'nrc' => ($root->filterXPath('.//nrc')->count() > 0) ? $root->filterXPath('.//nrc')->text() : null,
            'paymentDate' => ($root->filterXPath('.//fechapago')->count() > 0) ? $root->filterXPath('.//fechapago')->text() : null,
            'paymentHour' => ($root->filterXPath('.//horapago')->count() > 0) ? $root->filterXPath('.//horapago')->text() : null,
            'timestamp' => ($root->filterXPath('.//timestamp')->count() > 0) ? $root->filterXPath('.//timestamp')->text() : null,
            'type' => ($root->filterXPath('.//tipo')->count() > 0) ? $root->filterXPath('.//tipo')->text() : null,
            'entity' => ($root->filterXPath('.//entidad')->count() > 0) ? $root->filterXPath('.//entidad')->text() : null,
            'office' => ($root->filterXPath('.//oficina')->count() > 0) ? $root->filterXPath('.//oficina')->text() : null,
            'message' => $text_message,
        ];

        return $fields;
    }

    /**
     * Stores the payment confirmation in the database and return the Payment object.
     *
     * @param string $confirmation_payload
     *
     * @return Payment
     */
    public function process_payment_confirmation($confirmation_payload)
    {
        \parse_str($confirmation_payload, $params);
        if (!mb_check_encoding($params['param1'], "UTF8")) {
            $params['param1'] = mb_convert_encoding($params['param1'], "UTF8", "Windows-1252");
        }
        $fields = $this->__parse_confirmation_response($params['param1']);
        $payment = $this->pm->getRepository()->findOneBy([
            'registeredPaymentId' => $fields['id'],
        ]);
        if (null === $payment) {
            $payment = $this->pm->newPayment();
        }
        $payment->setStatus($fields['codigo']);
        $date = new \DateTime();
        // It automatically fills quantity, suffix and reference numbers
        $payment->setRegisteredPaymentId($fields['id']);
        $payment->setTimestamp($date->setTimestamp($fields['timestamp'] / 1000));
        $payment->setStatusMessage($fields['message']);
        $payment->setOperationNumber($fields['operationNumber']);
        $payment->setNrc($fields['nrc']);
        $payment->setPaymentDate($fields['paymentDate']);
        $payment->setPaymentHour($fields['paymentHour']);
        $payment->setType($fields['type']);
        $payment->setEntity($fields['entity']);
        $payment->setOffice($fields['office']);
        $payment->setMipagoResponse($params['param1']);
        $this->pm->savePayment($payment);

        return $payment;
    }

    public function getSuffixes()
    {
        return $this->suffixes;
    }
}
