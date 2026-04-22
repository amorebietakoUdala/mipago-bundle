<?php

namespace MiPago\Bundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Auditoria.
 */
#[ORM\Entity(repositoryClass: 'MiPago\Bundle\Repository\PaymentRepository')]
class Payment implements PaymentInterface, \Stringable
{
    final public const PAYMENT_STATUS_INITIALIZED = '01';
    final public const PAYMENT_STATUS_OK = '04';
    final public const PAYMENT_STATUS_NOK = '05';
    final public const PAYMENT_STATUS_DESCRIPTION = [
        self::PAYMENT_STATUS_INITIALIZED => 'status.initialized',
        self::PAYMENT_STATUS_OK => 'status.paid',
        self::PAYMENT_STATUS_NOK => 'status.unpaid',
    ];

    /**
     * @var int
     */
    protected $id;
    /**
     * @var \DateTime
     */
    protected $timestamp;
    /**
     * @var string
     */
    protected $referenceNumber;
    /**
     * @var string
     */
    protected $referenceNumberDC;
    /**
     * @var string
     */
    protected $suffix;
    /**
     * @var float
     */
    protected $quantity;
    /**
     * @var string
     */
    protected $registeredPaymentId;
    /**
     * @var string
     */
    protected $status;
    /**
     * @var string
     */
    protected $statusMessage;
    /**
     * @var string
     */
    protected $nrc;
    /**
     * @var string
     */
    protected $operationNumber;
    /**
     * @var string
     */
    protected $entity;
    /**
     * @var string
     */
    protected $office;
    /**
     * @var string
     */
    protected $paymentDate;
    /**
     * @var string
     */
    protected $paymentHour;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $surname1;
    /**
     * @var string
     */
    protected $surname2;
    /**
     * @var string
     */
    protected $city;
    /**
     * @var string
     */
    protected $nif;
    /**
     * @var string
     */
    protected $address;
    /**
     * @var string
     */
    protected $postalCode;
    /**
     * @var string
     */
    protected $territory;
    /**
     * @var string
     */
    protected $country;
    /**
     * @var string
     */
    protected $phone;
    /**
     * @var string
     */
    protected $email;
    /**
     * @var string
     */
    protected $mipagoResponse;

    public function getId()
    {
        return $this->id;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    public function getSuffix()
    {
        return $this->suffix;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getRegisteredPaymentId()
    {
        return $this->registeredPaymentId;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    public function getNrc()
    {
        return $this->nrc;
    }

    public function getOperationNumber()
    {
        return $this->operationNumber;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getOffice()
    {
        return $this->office;
    }

    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    public function getPaymentHour()
    {
        return $this->paymentHour;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSurname1()
    {
        return $this->surname1;
    }

    public function getSurname2()
    {
        return $this->surname2;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getNif()
    {
        return $this->nif;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function getTerritory()
    {
        return $this->territory;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getMipagoResponse()
    {
        return $this->mipagoResponse;
    }

    public function setTimestamp(\DateTime $timestamp = null)
    {
        if (null === $timestamp) {
            $this->timestamp = new \DateTime();
        } else {
            $this->timestamp = $timestamp;
        }

        return $this;
    }

    public function setReferenceNumber($reference_number)
    {
        $this->referenceNumber = $reference_number;
    }

    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setRegisteredPaymentId($registeredPaymentId)
    {
        $this->registeredPaymentId = $registeredPaymentId;
        if (null !== $registeredPaymentId) {
            $this->setReferenceNumberDC(substr((string) $registeredPaymentId, 11, 12));
            $this->setSuffix(substr((string) $registeredPaymentId, 24, 3));
            $this->setQuantity(floatval(substr((string) $registeredPaymentId, 33, 6).'.'.substr((string) $registeredPaymentId, 39, 2)));
        }
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setStatusMessage($statusMessage)
    {
        $this->statusMessage = $statusMessage;
    }

    public function setNrc($nrc)
    {
        $this->nrc = $nrc;
    }

    public function setOperationNumber($operationNumber)
    {
        $this->operationNumber = $operationNumber;
    }

    public function setOffice($office)
    {
        $this->office = $office;
    }

    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;
    }

    public function setPaymentHour($paymentHour)
    {
        $this->paymentHour = $paymentHour;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setSurname1($surname1)
    {
        $this->surname1 = $surname1;
    }

    public function setSurname2($surname2)
    {
        $this->surname2 = $surname2;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function setNif($nif)
    {
        $this->nif = $nif;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    public function setTerritory($territory)
    {
        $this->territory = $territory;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setMipagoResponse($mipagoResponse)
    {
        $this->mipagoResponse = $mipagoResponse;
    }

    public function isPaymentSuccessfull()
    {
        return self::PAYMENT_STATUS_OK === $this->status;
    }

    public function __toString(): string
    {
        return json_encode([
        'id' => $this->id,
        'timestamp' => $this->timestamp,
        'referenceNumber' => $this->referenceNumber,
        'referenceNumberDC' => $this->referenceNumberDC,
        'operationNumber' => $this->operationNumber,
        'suffix' => $this->suffix,
        'quantity' => $this->quantity,
        'registeredPaymentId' => $this->registeredPaymentId,
        'status' => $this->status,
        'statusMessage' => $this->statusMessage,
        'name' => $this->name,
        'surname1' => $this->surname1,
        'surname2' => $this->surname2,
        'city' => $this->city,
        'nif' => $this->nif,
        'address' => $this->address,
        'postalCode' => $this->postalCode,
        'territory' => $this->territory,
        'country' => $this->country,
        'phone' => $this->phone,
        'email' => $this->email,
        'mipagoResponse' => $this->mipagoResponse,
    ], JSON_THROW_ON_ERROR);
    }

    public function getReferenceNumberDC()
    {
        return $this->referenceNumberDC;
    }

    public function setReferenceNumberDC($referenceNumberDC)
    {
        $this->referenceNumberDC = $referenceNumberDC;
        if (null !== $referenceNumberDC) {
            $this->setReferenceNumber($referenceNumber = substr((string) $referenceNumberDC, 0, -2));
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'timestamp' => $this->timestamp,
            'referenceNumber' => $this->referenceNumber,
            'referenceNumberDC' => $this->referenceNumberDC,
            'operationNumber' => $this->operationNumber,
            'suffix' => $this->suffix,
            'quantity' => $this->quantity,
            'registeredPaymentId' => $this->registeredPaymentId,
            'status' => $this->status,
            'statusMessage' => $this->statusMessage,
            'name' => $this->name,
            'surname1' => $this->surname1,
            'surname2' => $this->surname2,
            'city' => $this->city,
            'nif' => $this->nif,
            'address' => $this->address,
            'postalCode' => $this->postalCode,
            'territory' => $this->territory,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'mipagoResponse' => $this->mipagoResponse,
        ];
    }
}
