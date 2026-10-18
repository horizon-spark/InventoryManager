<?php

namespace App\DataFixtures;

use App\Entity\Operation;
use App\Entity\Product;
use App\Entity\Warehouse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OperationFixtures extends Fixture implements DependentFixtureInterface
{
    // Строковые константы для type, т.к. колонка string(8), не enum
    private const TYPE_IN  = 'in';
    private const TYPE_OUT = 'out';

    public function load(ObjectManager $manager): void
    {
        // [productRef, warehouseRef, type, qty, daysAgo, comment]
        $rows = [
            // --- Ноутбуки ---
            [ProductFixtures::PROD_LAPTOP, WarehouseFixtures::WH_MAIN,    self::TYPE_IN,  20, 30, 'Первая поставка'],
            [ProductFixtures::PROD_LAPTOP, WarehouseFixtures::WH_MAIN,    self::TYPE_OUT,  5, 20, 'Продажа'],
            [ProductFixtures::PROD_LAPTOP, WarehouseFixtures::WH_MAIN,    self::TYPE_IN,  10, 10, 'Допоставка'],
            [ProductFixtures::PROD_LAPTOP, WarehouseFixtures::WH_MAIN,    self::TYPE_OUT,  7,  3, 'Продажа'],
            [ProductFixtures::PROD_LAPTOP, WarehouseFixtures::WH_RESERVE, self::TYPE_IN,   5, 15, 'Резерв'],

            // --- Мониторы ---
            [ProductFixtures::PROD_MONITOR, WarehouseFixtures::WH_MAIN,    self::TYPE_IN,  15, 25, 'Поставка'],
            [ProductFixtures::PROD_MONITOR, WarehouseFixtures::WH_MAIN,    self::TYPE_OUT,  4, 12, 'Продажа'],
            [ProductFixtures::PROD_MONITOR, WarehouseFixtures::WH_RESERVE, self::TYPE_IN,   3,  8, 'Резерв'],

            // --- Мыши ---
            [ProductFixtures::PROD_MOUSE, WarehouseFixtures::WH_MAIN, self::TYPE_IN,  50, 40, 'Оптовая закупка'],
            [ProductFixtures::PROD_MOUSE, WarehouseFixtures::WH_MAIN, self::TYPE_OUT, 15, 15, 'Продажи'],
            [ProductFixtures::PROD_MOUSE, WarehouseFixtures::WH_RETAIL, self::TYPE_IN, 20, 10, 'В розницу'],
            [ProductFixtures::PROD_MOUSE, WarehouseFixtures::WH_RETAIL, self::TYPE_OUT, 5,  4, 'Продажи'],

            // --- Кресла ---
            [ProductFixtures::PROD_CHAIR, WarehouseFixtures::WH_MAIN, self::TYPE_IN, 8, 20, 'Поставка'],
            [ProductFixtures::PROD_CHAIR, WarehouseFixtures::WH_MAIN, self::TYPE_OUT, 3, 6, 'Офис'],

            // --- Столы ---
            [ProductFixtures::PROD_DESK, WarehouseFixtures::WH_MAIN, self::TYPE_IN, 6, 18, 'Поставка'],

            // --- Дрели ---
            [ProductFixtures::PROD_DRILL, WarehouseFixtures::WH_MAIN, self::TYPE_IN,  4, 14, 'Поставка'],

            // --- Расходники ---
            [ProductFixtures::PROD_PAPER, WarehouseFixtures::WH_MAIN, self::TYPE_IN,  100, 60, 'Закупка на год'],
            [ProductFixtures::PROD_PAPER, WarehouseFixtures::WH_MAIN, self::TYPE_OUT, 40, 30, 'Расход'],
            [ProductFixtures::PROD_PAPER, WarehouseFixtures::WH_MAIN, self::TYPE_OUT, 25, 5,  'Расход'],
            [ProductFixtures::PROD_CARTRIDGE, WarehouseFixtures::WH_MAIN, self::TYPE_IN, 10, 45, 'Закупка'],
            [ProductFixtures::PROD_CARTRIDGE, WarehouseFixtures::WH_MAIN, self::TYPE_OUT, 3, 10, 'Замена'],
        ];

        foreach ($rows as [$productRef, $warehouseRef, $type, $qty, $daysAgo, $comment]) {
            $operation = (new Operation())
                ->setType($type)                // string(8)
                ->setQuantity($qty)
                ->setComment($comment)
                ->setCreatedAt(new \DateTimeImmutable("-{$daysAgo} days"))
                ->setProduct($this->getReference($productRef, Product::class))
                ->setWarehouse($this->getReference($warehouseRef, Warehouse::class));

            $manager->persist($operation);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ProductFixtures::class, WarehouseFixtures::class];
    }
}