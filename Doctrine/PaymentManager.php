<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MiPago\Bundle\Doctrine;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

// use MiPago\Bundle\Model\UserManager as BaseUserManager;
/**
 * Description of PaymentManager.
 *
 * @author ibilbao
 */
class PaymentManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    private $class;

    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->class = $class;
    }

    protected function getClass()
    {
        if (false !== strpos($this->class, ':')) {
            $metadata = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository()
    {
        return $this->objectManager->getRepository($this->getClass());
    }

    public function newPayment()
    {
        $class = $this->getClass();
        $payment = new $class();

        return $payment;
    }

    public function savePayment(\MiPago\Bundle\Model\PaymentInterface $payment)
    {
        $this->objectManager->persist($payment);
        $this->objectManager->flush();
    }
}
