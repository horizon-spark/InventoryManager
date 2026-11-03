<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название товара',
                'attr'  => ['class' => 'form-control'],
                // Валидация — в сущности Product (#[Assert\...])
            ])
            ->add('sku', TextType::class, [
                'label' => 'Артикул (SKU)',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'ABC-123',
                ],
            ])
            ->add('price', MoneyType::class, [
                'label'    => 'Цена',
                'currency' => 'RUB',
                'attr'     => ['class' => 'form-control'],
            ])
            // Обязательное поле связи ManyToOne -> Category
            ->add('category', EntityType::class, [
                'class'        => Category::class,
                'choice_label' => 'name',
                'label'        => 'Категория',
                'placeholder'  => '--- Выберите категорию ---',
                'required'     => true,
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr'  => ['class' => 'btn btn-success mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}