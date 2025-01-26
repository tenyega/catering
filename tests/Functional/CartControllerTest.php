<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CartControllerTest extends WebTestCase
{
    public function testAddToCart()
    {
        $client = static::createClient();
        $menuItemId = 1;  
        $crawler = $client->request('GET', '/cart/add/'.$menuItemId);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $session = $client->getRequest()->getSession();
        $cart = $session->get('cart', []);
        $this->assertArrayHasKey($menuItemId, $cart);
        $this->assertEquals(1, $cart[$menuItemId]);
    }

    public function testAddItemTwice()
    {
        $client = static::createClient();
        $menuItemId = 1; 
        $client->request('GET', '/cart/add/'.$menuItemId);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $client->request('GET', '/cart/add/'.$menuItemId);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $session = $client->getRequest()->getSession();
        $cart = $session->get('cart', []);
        $this->assertEquals(2, $cart[$menuItemId]);
    }
}