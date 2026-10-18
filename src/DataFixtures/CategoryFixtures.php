<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    // Имена референсов — чтобы другие фикстуры могли их переиспользовать
    public const CAT_ELECTRONICS = 'cat_electronics';
    public const CAT_FURNITURE   = 'cat_furniture';
    public const CAT_TOOLS       = 'cat_tools';
    public const CAT_CONSUMABLES = 'cat_consumables';

    public function load(ObjectManager $manager): void
    {
        $data = [
            self::CAT_ELECTRONICS => ['Электроника',   'Гаджеты, компьютеры, периферия'],
            self::CAT_FURNITURE   => ['Мебель',        'Столы, стулья, шкафы'],
            self::CAT_TOOLS       => ['Инструменты',   'Ручной и электроинструмент'],
            self::CAT_CONSUMABLES => ['Расходники',    'Бумага, картриджи, канцелярия'],
        ];

        foreach ($data as $ref => [$name, $description]) {
            $category = (new Category())
                ->setName($name)
                ->setDescription($description);

            $manager->persist($category);
            $this->addReference($ref, $category);
        }

        $manager->flush();
    }
}