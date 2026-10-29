<?php

namespace App\Form;

use App\Entity\Warehouse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class WarehouseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название склада',
                'attr'  => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(message: 'Укажите название'),
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('location', TextType::class, [
                'label'    => 'Расположение (опционально)',
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
            'data_class' => Warehouse::class,
        ]);
    }
}