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
        $totalSales = 0;
        $count = 0;
        $names = [
            "waffle",
            "tuna_tartare",
            "tiramisu",
            "takoyaki",
            "tacos",
            "sushi",
            "strawberry_shortcake",
            "steak",
            "spring_rolls",
            "spaghetti_carbonara",
            "spaghetti_bolognese",
            "shrimps_and_grits",
            "apple_pie",
            "baby_back_ribs",
            "seaweed_salad",
            "scallops",
            "sashimi",
            "samosa",
            "risotto",
            "red_velvet_cake",
            "ravioli",
            "ramen",
            "pulled_pork_sandwich",
            "prime_rib",
            "poutin",
            "pork_chop",
            "pizza",
            "pho",
            "peking_duck",
            "panna_cotta",
            "pancakes",
            "paella",
            "pad_thai",
            "onion_rings",
            "omelette",
            "mussels",
            "miso_soup",
            "macarons",
            "macaroni_cheese",
            "lasagna",
            "ice_cream",
            "hummus",
            "hot_dog",
            "hamburger",
            "fried_momo",
            "guacamole",
            "grilled_salmon",
            "grilled_cheese_sandwich",
            "greek_salad",
            "gnocchi",
            "garlic_bread",
            "frozen_yogurt",
            "fried_rice",
            "french_toast",
            "french_onion_soup",
            "beignets",
            "french_fries",
            "foie_gras",
            "fish_and_chips",
            "filet_mignon",
            "falafel",
            "escargots",
            "momo",
            "donuts",
            "deviled_eggs",
            "cup_cakes",
            "croque_madame",
            "creme_brulee",
            "club_sandwich",
            "churros",
            "chocolate_mousse",
            "chicken_wings",
            "chocolate_cake",
            "chicken_quesadilla",
            "chicken_curry",
            "cheesecake",
            "cheese_plate",
            "carrot_cake",
            "cannoli",
            "caprese_salade",
            "caesar_salad",
            "brealfast_burrito",
            "bread_pudding",
            "bibimbap",
            "beet_salad",
            "beef_tartare",
            "beef_carpaccio",
            "baklava",
        ];
        $dishesWithCategories = [
            // STARTER
            "tuna_tartare" => "STARTER",
            "takoyaki" => "STARTER",
            "spring_rolls" => "STARTER",
            "samosa" => "STARTER",
            "seaweed_salad" => "STARTER",
            "scallops" => "STARTER",
            "sashimi" => "STARTER",
            "foie_gras" => "STARTER",
            "deviled_eggs" => "STARTER",
            "caprese_salade" => "STARTER",
            "caesar_salad" => "STARTER",
            "beet_salad" => "STARTER",
            "guacamole" => "STARTER",
            "greek_salad" => "STARTER",
            "hummus" => "STARTER",
            "french_onion_soup" => "STARTER",
            "mussels" => "STARTER",
            "escargots" => "STARTER",
            "beef_tartare" => "STARTER",
            "beef_carpaccio" => "STARTER",

            // MAIN COURSE
            "steak" => "MAIN COURSE",
            "tacos" => "MAIN COURSE",
            "sushi" => "MAIN COURSE",
            "spaghetti_carbonara" => "MAIN COURSE",
            "spaghetti_bolognese" => "MAIN COURSE",
            "shrimps_and_grits" => "MAIN COURSE",
            "baby_back_ribs" => "MAIN COURSE",
            "ravioli" => "MAIN COURSE",
            "ramen" => "MAIN COURSE",
            "pulled_pork_sandwich" => "MAIN COURSE",
            "prime_rib" => "MAIN COURSE",
            "pork_chop" => "MAIN COURSE",
            "pizza" => "MAIN COURSE",
            "pho" => "MAIN COURSE",
            "peking_duck" => "MAIN COURSE",
            "paella" => "MAIN COURSE",
            "pad_thai" => "MAIN COURSE",
            "omelette" => "MAIN COURSE",
            "macaroni_cheese" => "MAIN COURSE",
            "lasagna" => "MAIN COURSE",
            "hamburger" => "MAIN COURSE",
            "fried_rice" => "MAIN COURSE",
            "fish_and_chips" => "MAIN COURSE",
            "filet_mignon" => "MAIN COURSE",
            "momo" => "MAIN COURSE",
            "chicken_quesadilla" => "MAIN COURSE",
            "chicken_curry" => "MAIN COURSE",
            "bibimbap" => "MAIN COURSE",
            "brealfast_burrito" => "MAIN COURSE",
            "risotto" => "MAIN COURSE",
            "grilled_salmon" => "MAIN COURSE",
            "gnocchi" => "MAIN COURSE",

            // DESSERT
            "waffle" => "DESSERT",
            "tiramisu" => "DESSERT",
            "strawberry_shortcake" => "DESSERT",
            "apple_pie" => "DESSERT",
            "red_velvet_cake" => "DESSERT",
            "panna_cotta" => "DESSERT",
            "pancakes" => "DESSERT",
            "macarons" => "DESSERT",
            "ice_cream" => "DESSERT",
            "frozen_yogurt" => "DESSERT",
            "beignets" => "DESSERT",
            "creme_brulee" => "DESSERT",
            "chocolate_mousse" => "DESSERT",
            "chocolate_cake" => "DESSERT",
            "carrot_cake" => "DESSERT",
            "donuts" => "DESSERT",
            "cup_cakes" => "DESSERT",
            "baklava" => "DESSERT",
            "bread_pudding" => "DESSERT",
            "churros" => "DESSERT",
            "cheesecake" => "DESSERT",
            "cannoli" => "DESSERT",
            "french_toast" => "DESSERT",

            // SNACKS
            "onion_rings" => "SNACKS",
            "miso_soup" => "SNACKS",
            "french_fries" => "SNACKS",
            "fried_momo" => "SNACKS",
            "grilled_cheese_sandwich" => "SNACKS",
            "club_sandwich" => "SNACKS",
            "croque_madame" => "SNACKS",
            "falafel" => "SNACKS",
            "hot_dog" => "SNACKS",
            "garlic_bread" => "SNACKS",
            "cheese_plate" => "SNACKS",
            "chicken_wings" => "SNACKS",
            "poutin" => "SNACKS",

            // BEVERAGE
            "coffee" => "BEVERAGE",
            "tea" => "BEVERAGE",
            "lemonade" => "BEVERAGE",
            "orange_juice" => "BEVERAGE",
            "mojito" => "BEVERAGE",
            "margarita" => "BEVERAGE",
            "soda" => "BEVERAGE",
            "iced_tea" => "BEVERAGE",
            "smoothie" => "BEVERAGE",
            "hot_chocolate" => "BEVERAGE",
            "milkshake" => "BEVERAGE",
            "espresso" => "BEVERAGE",
        ];



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
        // for ($i = 0; $i < 100; $i++) {
        //     $menuItem = new MenuItem();
        //     $menuItem->setAvailable($faker->boolean(90))
        //         ->setCategory($faker->randomElement($categoryArray))
        //         ->setDescription($faker->paragraph(3, true))
        //         ->setName($faker->randomElement($names))
        //         ->setPrice($faker->randomFloat(2, 0, 100))
        //         ->setImg("img1.jpg")
        //     ;
        //     $menuItemArray[] = $menuItem;
        //     $manager->persist($menuItem);
        // }

        foreach ($dishesWithCategories as $name => $category) {
            $menuItem = new MenuItem();
            $menuItem->setAvailable($faker->boolean(90))
                ->setCategory($category)
                ->setDescription($faker->paragraph(3, true))
                ->setName($name)
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
