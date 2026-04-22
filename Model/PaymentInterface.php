<?php

namespace MiPago\Bundle\Model;

/**
 * The interface that specifies a Payment made on MiPago.
 *
 * @author ibilbao
 */
interface PaymentInterface
{
    public function getId();

    public function getTimestamp(): \DateTime;

    public function getReferenceNumber();

    public function getSuffix();

    public function getQuantity(): float;

    public function getRegisteredPaymentId();

    public function getStatus();

    public function getStatusMessage();

    public function getNrc();

    public function getOperationNumber();

    public function getEntity();

    public function getOffice();

    public function getPaymentDate();

    public function getPaymentHour();

    public function getType();

    public function getName();

    public function getSurname1();

    public function getSurname2();

    public function getCity();

    public function getNif();

    public function getAddress();

    public function getPostalCode();

    public function getTerritory();

    public function getCountry();

    public function getPhone();

    public function getEmail();

    public function getMipagoResponse();

    public function setTimestamp(\DateTime $timestamp = null);

    public function setReferenceNumber($referenceNumber);

    public function setSuffix($suffix);

    public function setQuantity($quantity);

    public function setRegisteredPaymentId($registeredPaymentId);

    public function setStatus($status);

    public function setStatusMessage($statusMessage);

    public function setNrc($nrc);

    public function setOperationNumber($operationNumber);

    public function setOffice($office);

    public function setPaymentDate($paymentDate);

    public function setPaymentHour($paymentHour);

    public function setEntity($entity);

    public function setType($type);

    public function setName($name);

    public function setSurname1($surname_1);

    public function setSurname2($surname_2);

    public function setCity($city);

    public function setNif($nif);

    public function setAddress($address);

    public function setPostalCode($postalCode);

    public function setTerritory($territory);

    public function setCountry($country);

    public function setPhone($phone);

    public function setEmail($email);

    public function setMipagoResponse($mipagoResponse);

    public function isPaymentSuccessfull();

    public function getReferenceNumberDC();

    public function setReferenceNumberDC($referenceNumberDC);

    /**
     * Return the array version of the payment.
     *
     * @return array
     */
    public function toArray();
}
