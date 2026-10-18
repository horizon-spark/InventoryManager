<?php

namespace App\DataFixtures;

use App\Entity\Warehouse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WarehouseFixtures extends Fixture
{
    public const WH_MAIN    = 'wh_main';
    public const WH_RESERVE = 'wh_reserve';
    public const WH_RETAIL  = 'wh_retail';

    public function load(ObjectManager $manager): void
    {
        $data = [
            self::WH_MAIN    => ['Основной склад',  'Москва, ул. Складская, 1'],
            self::WH_RESERVE => ['Резервный склад', 'Химки, Промзона, 12'],
            self::WH_RETAIL  => ['Розничный склад','Санкт-Петербург, Невский пр., 100'],
        ];

        foreach ($data as $ref => [$name, $location]) {
            $warehouse = (new Warehouse())
                ->setName($name)
                ->setLocation($location);

            $manager->persist($warehouse);
            $this->addReference($ref, $warehouse);
        }

        $manager->flush();
    }
}