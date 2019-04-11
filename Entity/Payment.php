<?php

namespace MiPago\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Auditoria.
 *
 * @ORM\Table(name="Payments")
 * @ORM\Entity(repositoryClass="MiPago\Bundle\Repository\PaymentRepository")
 */
class Payment
{
    const PAYMENT_STATUS_INITIALIZED = '01';

    const PAYMENT_STATUS_OK = '04';

    const PAYMENT_STATUS_NOK = '05';

    const PAYMENT_STATUS_DESCRIPTION = [
    self::PAYMENT_STATUS_INITIALIZED => 'status.initialized',
    self::PAYMENT_STATUS_OK => 'status.paid',
    self::PAYMENT_STATUS_NOK => 'status.unpaid',
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="reference_number", type="string", length=12)
     */
    private $reference_number;

    /**
     * Reference Number with control digits.
     *
     * @var string
     *
     * @ORM\Column(name="reference_number_dc", type="string", length=12)
     */
    private $reference_number_DC;

    /**
     * @var string
     *
     * @ORM\Column(name="suffix", type="string", length=3)
     */
    private $suffix;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="decimal", precision=6, scale=2 )
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="registered_payment_id", type="string", length=42, nullable=true, unique=true)
     */
    private $registered_payment_id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="status_message", type="string", nullable=true)
     */
    private $statusMessage;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $nrc;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $operationNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $entity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $office;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $paymentDate;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $paymentHour;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname_1", type="string", nullable=true)
     */
    private $surname_1;

    /**
     * @var string
     *
     * @ORM\Column(name="surname_2", type="string", nullable=true)
     */
    private $surname_2;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="nif", type="string", nullable=true)
     */
    private $nif;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="territory", type="string", nullable=true)
     */
    private $territory;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="response", type="string", length=4000, nullable=true)
     */
    private $mipagoResponse;

    public function getId()
    {
        return $this->id;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function getReference_number()
    {
        return $this->reference_number;
    }

    public function getSuffix()
    {
        return $this->suffix;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getRegistered_payment_id()
    {
        return $this->registered_payment_id;
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

    public function getSurname_1()
    {
        return $this->surname_1;
    }

    public function getSurname_2()
    {
        return $this->surname_2;
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
        $this->timestamp = new \DateTime();
    }

    public function setReference_number($reference_number)
    {
        $this->reference_number = $reference_number;
    }

    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setRegistered_payment_id($registered_payment_id)
    {
        $this->registered_payment_id = $registered_payment_id;
        if (null !== $registered_payment_id) {
            $this->setReference_number_DC(substr($registered_payment_id, 11, 12));
            $this->setSuffix(substr($registered_payment_id, 24, 3));
            $this->setQuantity(floatval(substr($registered_payment_id, 33, 6).'.'.substr($registered_payment_id, 39, 2)));
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

    public function setSurname_1($surname_1)
    {
        $this->surname_1 = $surname_1;
    }

    public function setSurname_2($surname_2)
    {
        $this->surname_2 = $surname_2;
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

    public function __toString()
    {
        return json_encode([
        'id' => $this->id,
        'timestamp' => $this->timestamp,
        'reference_number' => $this->reference_number,
        'reference_number_DC' => $this->reference_number_DC,
        'operation_number' => $this->operationNumber,
        'suffix' => $this->suffix,
        'quantity' => $this->quantity,
        'registered_payment_id' => $this->registered_payment_id,
        'status' => $this->status,
        'statusMessage' => $this->statusMessage,
        'name' => $this->name,
        'surname_1' => $this->surname_1,
        'surname_2' => $this->surname_2,
        'city' => $this->city,
        'nif' => $this->nif,
        'address' => $this->address,
        'postalCode' => $this->postalCode,
        'territory' => $this->territory,
        'country' => $this->country,
        'phone' => $this->phone,
        'email' => $this->email,
        'mipagoResponse' => $this->mipagoResponse,
    ]);
    }

    public function getReference_number_DC()
    {
        return $this->reference_number_DC;
    }

    public function setReference_number_DC($reference_number_DC)
    {
        $this->reference_number_DC = $reference_number_DC;
        if (null !== $reference_number_DC) {
            $this->setReference_number($referenceNumber = substr($reference_number_DC, 0, -2));
        }

        return $this;
    }

    public function toArray()
    {
        return [
        'id' => $this->id,
        'timestamp' => $this->timestamp,
        'reference_number' => $this->reference_number,
        'reference_number_DC' => $this->reference_number_DC,
        'operation_number' => $this->operationNumber,
        'suffix' => $this->suffix,
        'quantity' => $this->quantity,
        'registered_payment_id' => $this->registered_payment_id,
        'status' => $this->status,
        'statusMessage' => $this->statusMessage,
        'name' => $this->name,
        'surname_1' => $this->surname_1,
        'surname_2' => $this->surname_2,
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
