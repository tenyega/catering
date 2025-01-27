<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MyControllerTest extends WebTestCase
{
    public function testPageLoad()
    {
        putenv('DATABASE_URL=mysql://root:@127.0.0.1:3306/catering?serverVersion=8.0.32&charset=utf8mb4');

        $client = static::createClient();
        $crawler = $client->request('GET', '/my-page');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tibet Restaurant');

    }
}
