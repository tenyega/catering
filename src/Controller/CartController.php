<?php
// src/Controller/CartController.php
namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;

use App\Repository\MenuItemRepository;
use App\Repository\OrderRepository;
use App\Service\CartService;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cartService;
    private $mir;
    private $or;
    public function __construct(CartService $cartService, MenuItemRepository $mir, OrderRepository $or)
    {
        $this->cartService = $cartService;
        $this->mir = $mir;
        $this->or = $or;
    }


    #[Route('/cart/add/{id}', name: 'cart_add', requirements: ['id' => '\d+'])]
    public function addToCart($id,  MenuItemRepository $mir, SessionInterface $sessionInterface, Request $request)
    {

        $menuItem = $mir->find($id);
        if (!$menuItem) {
            throw $this->createNotFoundException("The MenuItem doesnt exist Sorry");
        }
        $cart = $sessionInterface->get('cart', []);
        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $sessionInterface->set('cart', $cart);

        $referer = $request->headers->get('referer');


        // If referer is not empty, redirect to it, otherwise, redirect to a default route
        if ($referer) {

            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', requirements: ['id' => '\d+'])]
    public function removeFromCart($id,  MenuItemRepository $mir, SessionInterface $sessionInterface, Request $request)
    {

        $menuItem = $mir->find($id);
        if (!$menuItem) {
            throw $this->createNotFoundException("The MenuItem doesn't exist. Sorry");
        }

        $cart = $sessionInterface->get('cart', []);

        // Check if the item exists in the cart
        if (array_key_exists($id, $cart)) {
            // If the quantity is greater than 0, decrement it
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                // Otherwise, remove the item from the cart entirely
                unset($cart[$id]);
            }
        }

        // Update the session with the modified cart
        $sessionInterface->set('cart', $cart);
        $referer = $request->headers->get('referer');

        // If referer is not empty, redirect to it, otherwise, redirect to a default route
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_home');
    }





    #[Route('/cart', name: 'cart_show')]
    public function show(SessionInterface $sessionInterface, MenuItemRepository $mir)
    {
        $detailedCart = [];
        $total = 0;
        $totalQuantity = 0;
        foreach ($sessionInterface->get('cart', []) as $id => $qty) {
            $menuItem = $mir->find($id);
            $detailedCart[] = [
                'menuItem' => $menuItem,
                'qty' => $qty,

            ];
            $totalQuantity += $qty;
            $total += ($menuItem->getPrice() * $qty);
        }
        


        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'totalQuantity' => $totalQuantity
        ]);
    }
}
