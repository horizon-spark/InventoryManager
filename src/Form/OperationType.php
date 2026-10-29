<?php

namespace App\Form;

use App\Entity\Operation;
use App\Entity\Product;
use App\Entity\Warehouse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label'   => 'Тип операции',
                'choices' => [
                    'Приход (in)'  => 'in',
                    'Расход (out)' => 'out',
                ],
                'placeholder' => 'Выберите тип',
                'attr'        => ['class' => 'form-select'],
                'constraints' => [
                    new Assert\NotBlank(message: 'Выберите тип операции'),
                    new Assert\Length(max: 8),
                ],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Количество',
                'attr'  => ['class' => 'form-control', 'min' => 1],
                'constraints' => [
                    new Assert\NotNull(message: 'Укажите количество'),
                    new Assert\Positive(message: 'Количество должно быть больше нуля'),
                ],
            ])
            ->add('product', EntityType::class, [
                'class'        => Product::class,
                'choice_label' => 'name',
                'label'        => 'Товар',
                'placeholder'  => 'Выберите товар',
                'attr'         => ['class' => 'form-select'],
                'constraints'  => [
                    new Assert\NotNull(message: 'Выберите товар'),
                ],
            ])
            ->add('warehouse', EntityType::class, [
                'class'        => Warehouse::class,
                'choice_label' => 'name',
                'label'        => 'Склад',
                'placeholder'  => 'Выберите склад',
                'attr'         => ['class' => 'form-select'],
                'constraints'  => [
                    new Assert\NotNull(message: 'Выберите склад'),
                ],
            ])
            ->add('comment', TextType::class, [
                'label'    => 'Комментарий (опционально)',
                'required' => false,
                'attr'     => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr'  => ['class' => 'btn btn-success mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Operation::class,
        ]);
    }
}