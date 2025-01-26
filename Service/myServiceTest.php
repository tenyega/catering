<?php

namespace App\Tests\Service;

use App\Entity\MenuItem;
use App\Service\CartService;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;

class MyServiceTest extends TestCase
{
    private $em;
    private $security;
    private $ur;
    private $menuItem;
    public function __construct(EntityManagerInterface $em, SecurityBundleSecurity $security, UserRepository $ur)
    {
        $this->em = $em;
        $this->security = $security;
        $this->ur = $ur;
    }
    public function testOrderTotal()
    {

        $menuItem = new MenuItem();
        $menuItem->setAvailable(true)
            ->setCategory('STARTER')
            ->setDescription('This is a test menu item added through a cart test ')
            ->setImg('IMMMM.jpg')
            ->setName('TEST MENU')
            ->setPrice('11.1');
        $service = new CartService($this->em, $this->security, $this->ur);
        $result = $service->addItemToCart($menuItem, 5);
        echo $result;
        // $this->assertEquals(15, $result);
    }
}
