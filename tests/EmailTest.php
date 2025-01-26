<?php

namespace App\Tests\Integration;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class EmailTest extends KernelTestCase
{
    public function testGetUserByEmail()
    {
        self::bootKernel();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        // Fetch the user from the repository based on the email
        $user = $userRepository->findOneByEmail('user1@email.com');
        $this->assertNotNull($user);  // Ensure the user is found
        $this->assertEquals('user1@email.com', $user->getEmail()); 
    }

    public function testGetUserByEmailNotFound()
    {
        self::bootKernel();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('nonexistent@email.com');
        $this->assertNull($user);  
    }

    public function testGetUserByEmailCaseInsensitive()
    {
        self::bootKernel();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('USER1@EMAIL.COM');
        $this->assertNotNull($user);  
        $this->assertEquals('user1@email.com', $user->getEmail());  
    }
}
