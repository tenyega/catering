<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\MenuItem;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Security\Core\Security;

class CartService
{
    private $em;
    private $security;
    private $userRepo;
    public function __construct(EntityManagerInterface $em, SecurityBundleSecurity $security,UserRepository $cr)
    {
        $this->em = $em;
        $this->security = $security;
        $this->userRepo = $cr;
    }

    public function addItemToCart(MenuItem $menuItem, int $quantity): Order
    {
        // Get the current user
        //$user = $this->security->getUser();
        $user = $this->userRepo->findOneBy(['id' => "151"]);
        // Fetch or create a "cart" order
        $cart = $this->getCartForUser($user);
        if (!$cart) {
            $cart = new Order();

            $cart->setUser($user);
            $cart->setOrderDate(new \DateTimeImmutable());
            $cart->setOrderStatus("INCART");
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

    private function getCartForUser($user): ?Order
    {
        return $this->em->getRepository(Order::class)->findOneBy([
            'user' => $user,
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
