<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Employee;
use App\Entity\MenuItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Payment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->hasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $paymentMethod = ['CARD', 'CASH'];
        $customerArray = [];
        $paymentStatus = ['PAID', 'PENDING', 'CANCELLED'];
        $orderStatus = ['RECEIVED', 'PENDING', 'PROCESSED', 'PROCESSING'];
        $menuItemArray = [];
        $employeeArray = [];
        $orderTotals = []; // Temporary array to store totals for each Order
        $categoryArray = ['STARTER', 'MAIN COURSE', 'DESSERT', 'SNACKS', 'BEVERAGE'];
        $totalSales = 0;
        $count = 0;


        for ($i = 0; $i < 30; $i++) {
            $customer = new Customer();
            $customer->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setAddress($faker->address())
                ->setPhone($faker->phoneNumber())
                ->setEmail("cus" . $i . "@email.com")
                ->setPassword($this->hasher->hashPassword($customer, 'cus' . $i))
                ->setRoles(['ROLE_USER']);
            $customerArray[] = $customer;
            $manager->persist($customer);
        }

        // CREATING 3 different employees
        $employee1 = new Employee();
        $employee1->setEmail("em1@email.com")
            ->setPassword($this->hasher->hashPassword($employee1, 'em1'))
            ->setRoles(['ROLE_EMPLOYEE'])
        ;
        $employeeArray[] = $employee1;
        $manager->persist($employee1);

        $employee2 = new Employee();
        $employee2->setEmail("em2@email.com")
            ->setPassword($this->hasher->hashPassword($employee2, 'em2'))
            ->setRoles(['ROLE_EMPLOYEE'])
        ;
        $employeeArray[] = $employee2;
        $manager->persist($employee2);

        $employee3 = new Employee();
        $employee3->setEmail("em3@email.com")
            ->setPassword($this->hasher->hashPassword($employee3, 'em3'))
            ->setRoles(['ROLE_EMPLOYEE'])
        ;
        $employeeArray[] = $employee3;
        $manager->persist($employee3);


        $admin = new Employee();
        $admin->setEmail("admin@email.com")
            ->setPassword($this->hasher->hashPassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN'])
        ;
        $employeeArray[] = $admin;
        $manager->persist($admin);



        //CREATING MENU ITEMS 
        for ($i = 0; $i < 100; $i++) {
            $menuItem = new MenuItem();
            $menuItem->setAvailable($faker->boolean(90))
                ->setCategory($faker->randomElement($categoryArray))
                ->setDescription($faker->paragraph(3, true))
                ->setName($faker->name())
                ->setPrice($faker->randomFloat(2, 0, 100))
                ->setImg("img1.jpg")
            ;
            $menuItemArray[] = $menuItem;
            $manager->persist($menuItem);
        }



        // Step 1: Creating 30 different orders
        for ($i = 0; $i < 30; $i++) {
            $order = new Order();
            $order
                ->setCustomer($faker->randomElement($customerArray))
                ->setEmployee($faker->randomElement($employeeArray))
                ->setPaymentStatus($faker->randomElement($paymentStatus))
                ->setOrderStatus($faker->randomElement($orderStatus))
            ;

            $orderArray[] = $order; // Store Order in array for easy reference
            $orderTotals[spl_object_id($order)] = 0; // Initialize total for this order in $orderTotals

            $manager->persist($order);
        }

        // Step 2: Creating 50 different OrderItems
        for ($i = 0; $i < 50; $i++) {
            $orderItem = new OrderItem();
            $orderItem
                ->setQuantity($faker->randomDigitNotZero()) // Ensure quantity is not zero
                ->setMenuItem($faker->randomElement($menuItemArray))
            ;

            // Calculate item total and assign it
            $itemPrice = $orderItem->getMenuItem()->getPrice() * $orderItem->getQuantity();
            $orderItem->setItemPrice($itemPrice);

            // Assign a random order to this order item
            $order = $faker->randomElement($orderArray);
            $orderItem->setOrders($order);

            // Accumulate the total amount for this order in $orderTotals
            $orderTotals[spl_object_id($order)] += $itemPrice;

            $manager->persist($orderItem);
        }

        // Step 3: Update each Order with the calculated total amount
        foreach ($orderArray as $order) {
            $orderId = spl_object_id($order);
            $order->setTotalAmount($orderTotals[$orderId]); // Update Order with accumulated total
            if ($order->getTotalAmount() == 0) {
                $order->setPaymentStatus("RECEIVED");
            }
        }




        //Payment 
        // Define the constant for PAID status if you don't already have it, e.g., "PAID"


        // Creating 15 different payments
        // Creating 15 different payments
        foreach ($orderArray as $order) {


            // Ensure the order has an amount and is marked as PAID
            if ($order->getPaymentStatus() === 'PAID') {
                $payment = new Payment();

                // Set the order and amount for the payment
                $payment->setOrderId($order)
                    ->setAmount($order->getTotalAmount())
                    ->setPaymentMethod($faker->randomElement($paymentMethod));

                // Update sales and payment count
                $totalSales += $payment->getAmount();
                $count++;

                // Persist the payment
                $manager->persist($payment);
            }
        }

        // Flush to actually save all persisted entities
        $manager->flush();
    }
}
