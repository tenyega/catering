<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\MenuItem;
use App\Repository\CustomerRepository;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Security\Core\Security;

class CartService
{
    private $em;
    private $security;
    private $customerRepo;
    private $employeeRepo;
    public function __construct(EntityManagerInterface $em, SecurityBundleSecurity $security, CustomerRepository $cr, EmployeeRepository $er)
    {
        $this->em = $em;
        $this->security = $security;
        $this->customerRepo = $cr;
        $this->employeeRepo = $er;
    }

    public function addItemToCart(MenuItem $menuItem, int $quantity): Order
    {
        // Get the current user
        //$customer = $this->security->getUser();
        $customer = $this->customerRepo->findOneBy(['id' => "151"]);
        $employee = $this->employeeRepo->findOneBy(['id' => "22"]);
        // Fetch or create a "cart" order
        $cart = $this->getCartForCustomer($customer);
        if (!$cart) {
            $cart = new Order();

            $cart->setCustomer($customer);
            $cart->setOrderDate(new \DateTimeImmutable());
            $cart->setOrderStatus("INCART");
            $cart->setEmployee($employee);
            $cart->setPaymentStatus("PROCESSING");
            $this->em->persist($cart);
        }

        // Check if item is already in the cart
        $existingOrderItem = null;
        foreach ($cart->getOrderItems() as $orderItem) {
            if ($orderItem->getMenuItem()->getId() === $menuItem->getId()) {
                $existingOrderItem = $orderItem;
                break;
            }
        }

        if ($existingOrderItem) {
            // Update quantity if item already exists in cart
            $existingOrderItem->setQuantity($existingOrderItem->getQuantity() + $quantity);
        } else {
            // Otherwise, create a new OrderItem
            $orderItem = new OrderItem();
            $orderItem->setOrders($cart);
            $orderItem->setMenuItem($menuItem);
            $orderItem->setQuantity($quantity);
            $orderItem->setItemPrice($menuItem->getPrice());
            $this->em->persist($orderItem);
        }

        // Update total amount and save
        $cart->setTotalAmount($this->calculateCartTotal($cart));
        $this->em->flush();

        return $cart;
    }

    private function getCartForCustomer($customer): ?Order
    {
        return $this->em->getRepository(Order::class)->findOneBy([
            'customer' => $customer,
            'orderStatus' => 'cart'
        ]);
    }

    public function calculateCartTotal(Order $cart): float
    {
        $total = 0;
        foreach ($cart->getOrderItems() as $item) {
            $total += $item->getItemPrice() * $item->getQuantity();
        }
        return $total;
    }


    public function getCartSummary()
    {
        // Retrieve the cart summary from CartService (total quantity and total amount)
        return [
            'totalQuantity' => "222",
            'totalAmount' => "333",
        ];
    }
}
