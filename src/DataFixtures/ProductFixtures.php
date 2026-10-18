<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category; 
use App\DataFixtures\CategoryFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROD_LAPTOP      = 'prod_laptop';
    public const PROD_MONITOR     = 'prod_monitor';
    public const PROD_MOUSE       = 'prod_mouse';
    public const PROD_CHAIR       = 'prod_chair';
    public const PROD_DESK        = 'prod_desk';
    public const PROD_DRILL       = 'prod_drill';
    public const PROD_PAPER       = 'prod_paper';
    public const PROD_CARTRIDGE   = 'prod_cartridge';

    public function load(ObjectManager $manager): void
    {
        $data = [
            // ref                => [name,                 sku,          price,     categoryRef]
            self::PROD_LAPTOP    => ['Ноутбук Pro 15',      'NB-001',    '79999.00', CategoryFixtures::CAT_ELECTRONICS],
            self::PROD_MONITOR   => ['Монитор 27" 4K',      'MON-27-4K', '32999.00', CategoryFixtures::CAT_ELECTRONICS],
            self::PROD_MOUSE     => ['Мышь беспроводная',   'MOU-WL-01',  '2999.00', CategoryFixtures::CAT_ELECTRONICS],
            self::PROD_CHAIR     => ['Кресло офисное',      'CHR-OF-01', '15999.00', CategoryFixtures::CAT_FURNITURE],
            self::PROD_DESK      => ['Стол письменный',     'DSK-01',     '9999.00', CategoryFixtures::CAT_FURNITURE],
            self::PROD_DRILL     => ['Дрель аккумуляторная','DRL-18V',   '12999.00', CategoryFixtures::CAT_TOOLS],
            self::PROD_PAPER     => ['Бумага А4 (500 л.)',  'PAP-A4-500',  '399.00', CategoryFixtures::CAT_CONSUMABLES],
            self::PROD_CARTRIDGE => ['Картридж лазерный',   'CRT-LZ-01',  '2499.00', CategoryFixtures::CAT_CONSUMABLES],
        ];

        foreach ($data as $ref => [$name, $sku, $price, $categoryRef]) {
            $product = (new Product())
                ->setName($name)
                ->setSku($sku)
                ->setPrice($price)
                ->setCategory($this->getReference($categoryRef, Category::class));

            $manager->persist($product);
            $this->addReference($ref, $product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}