<?php

namespace App\Tests\Unit;

use App\Entity\MenuItem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MenuItemTest extends KernelTestCase
{
    public function testEntity(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $menuItem = new MenuItem();
        $menuItem->setAvailable(true)
            ->setCategory('STARTER')
            ->setDescription('This is a test ')
            ->setImg('test.jpg')
            ->setName('Test')
            ->setPrice('111');

        $errors = $container->get('validator')->validate($menuItem);
        $this->assertCount(0, $errors);
    }
}
