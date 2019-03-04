<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MiPago\Bundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use MiPago\Bundle\Entity\Payment;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
/**
 * Description of ReceiptBilatzaileaForm
 *
 * @author ibilbao
 */
class PaymentTypeForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
	$search = $options['search'];
	$readonly = $options['readonly'];
	if ($search) {
	    $builder->add('date_from', DateTimeType::class, [
		'widget' => 'single_text',
		'html5' => 'false',
		'format' => 'yyyy-MM-dd HH:mm:ss',
		'attr' => [ 'class' => 'js-datepicker'],
		'label'=>'payment.from',
	    	'disabled' => $readonly,
	    ])
	    ->add('date_to', DateTimeType::class, [
		'widget' => 'single_text',
		'html5' => 'false',
		'format' => 'yyyy-MM-dd HH:mm:ss',
		'attr' => [ 'class' => 'js-datepicker'],
		'label'=>'payment.to',
	    	'disabled' => $readonly,
	    ]);
	} else {
	    $builder->add('timestamp', DateTimeType::class, [
		'widget' => 'single_text',
		'html5' => 'false',
		'format' => 'yyyy-MM-dd HH:mm:ss',
		'attr' => [ 'class' => 'js-datepicker'],
		'label'=>'payment.timestamp',
	    	'disabled' => $readonly,
	    ]);
	}
	$builder->add('reference_number',null,[
		'label'=>'payment.reference_number',
	    	'disabled' => $readonly,
	    ])
	    ->add('suffix',null,[
		'label'=>'payment.suffix',
	    	'disabled' => $readonly,
	    ])
	    ->add('registered_payment_id',null,[
		'label'=>'payment.registered_payment_id',
	    	'disabled' => $readonly,
	    ])
	    ->add('status', ChoiceType::class,[
		'choices' => [
		    'status.any' => null,
		    'status.initialized' => Payment::PAYMENT_STATUS_INITIALIZED,
		    'status.paid' => Payment::PAYMENT_STATUS_OK,
		    'status.unpaid' => Payment::PAYMENT_STATUS_NOK,
		],
		'label'=>'payment.status',
	    	'disabled' => $readonly,
	    ])
	    ->add('nif',null,[
		'label'=>'payment.nif',
	    	'disabled' => $readonly,
	    ])
	    ->add('email',null,[
		'label'=>'payment.email',
	    	'disabled' => $readonly,
	    ]);
	if (!$search) {
	    $builder->add('status_message',null,[
		    'label'=>'payment.status_message',
		    'disabled' => $readonly,
		])
		->add('name',null,[
		    'label'=>'payment.name',
		    'disabled' => $readonly,
		])
		->add('surname_1',null,[
		    'label'=>'payment.surname_1',
		    'disabled' => $readonly,
		])
		->add('surname_2',null,[
		    'label'=>'payment.surname_2',
		    'disabled' => $readonly,
		])
		->add('nif',null,[
		    'label'=>'payment.nif',
		    'disabled' => $readonly,
		])
		->add('phone',null,[
		    'label'=>'payment.phone',
		    'disabled' => $readonly,
		])
		->add('operation_number',null,[
		    'label'=>'payment.operation_number',
		    'disabled' => $readonly,
	    ]);
	}
	if ( $search ) {
	    $builder->add('search', SubmitType::class,[
		'label'=>'btn.search',
	    	'disabled' => $readonly,
	    ]);
	} else {
	    $builder->add('back', ButtonType::class,[
		'label'=>'btn.back',
	    ]);
	}
//	    ->add('telefono')
//	    ->add('importe')
//	    ->add('noiztik', DateTimeType::class, [
//		'widget' => 'single_text',
//		'html5' => 'false',
//		'format' => 'yyyy-MM-dd HH:mm',
//		'attr' => [ 'class' => 'js-datepicker'],
//	    ])
//	    ->add('nora', DateTimeType::class, [
//		'widget' => 'single_text',
//		'html5' => 'false',
//		'format' => 'yyyy-MM-dd HH:mm',
//		'attr' => [ 'class' => 'js-datepicker'],
//	    ])
//	    ->add('egoera', EntityType::class,[
//		'placeholder'=> 'messages.hautatu_egoera',
//		'class' => Egoera::class,
//		'choice_label' => function ($egoera) use ($locale) {
//		    if ($locale === 'es') {
//			return $egoera->getDeskripzioaEs();
//		    } else {
//			return $egoera->getDeskripzioaEu();
//		    }
//		},
//	    ])
//	    ->add('kalea', TextType::class,[
//	    ]);
//	    if (in_array('ROLE_ADMIN', $options['data']['role']) || in_array('ROLE_INFORMATZAILEA', $options['data']['role']) || in_array('ROLE_ARDURADUNA', $options['data']['role'])) {
//		$builder->add('enpresa', EntityType::class,[
//		    'placeholder'=> 'messages.hautatu_enpresa',
//		    'class' => Enpresa::class,
//		    'query_builder' => function (EnpresaRepository $repo) {
//			    return $repo->createOrderedQueryBuilder();
//			}
//		    ])
//		    ->add('zerbitzua', EntityType::class,[
//		    'placeholder'=> 'messages.hautatu_zerbitzua',
//		    'class' => Zerbitzua::class,
//		    'group_by' => 'enpresa',
//		    'choice_label' => function ($zerbitzua) use ($locale) {
//		    if ($locale === 'es') {
//			return $zerbitzua->getIzenaEs();
//		    } else {
//			return $zerbitzua->getIzenaEu();
//		    }
//		    },
//		    'query_builder' => function (ZerbitzuaRepository $repo) {
//			    return $repo->createOrderedQueryBuilder();
//		    }
//		])
//		;
//	    } else {
//		$builder->add('zerbitzua', EntityType::class,[
//		    'placeholder'=> 'messages.hautatu_zerbitzua',
//		    'class' => Zerbitzua::class,
//		    'query_builder' => function (ZerbitzuaRepository $repo) use ($options) {
//			$enpresa = null;
//			if (array_key_exists('enpresa', $options['data'])) {
//			    $enpresa = $options['data']['enpresa'];
//			}
//			return $repo->createZerbitzuakQueryBuilder($enpresa);
//		}
//		])
	;
//	    }
    }

    public function configureOptions(OptionsResolver $resolver) {
	$resolver->setDefaults([
	    'csrf_protection' => true,
	    'data_class' => null, 
	    'translation_domain' => 'mipago',
	    'readonly' => null,
	    'search' => true,
	]);
    }
}
