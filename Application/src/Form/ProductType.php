<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new NotNull(),
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'price',
                MoneyType::class,
                [
                    'divisor' => 100,
                    'constraints' => [
                        new NotNull(),
                        new NotBlank(),
                        new GreaterThan(0),
                    ],
                ]
            )
            ->add(
                'stock',
                IntegerType::class,
                [
                    'constraints' => [
                        new NotNull(),
                        new NotBlank(),
                        new GreaterThan(0),
                    ],
                ]
            );
    }

    public function getBlockPrefix(){
        return '';
    }
}
