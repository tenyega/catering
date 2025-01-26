
<?php
namespace App\Tests\Service;


use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\MenuItem;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;


class CartServiceTest extends TestCase
{
    private $entityManager;
    private $security;
    private $userRepository;
    private $cartService;

    protected function setUp(): void
    {
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->security = $this->createMock(SecurityBundleSecurity::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->cartService = new CartService(
            $this->entityManager,
            $this->security,
            $this->userRepository
        );
    }

    public function testAddItemToCart()
    {
        $user = new User();
        $menuItem = new MenuItem();
        $menuItem->setId(1);
        $menuItem->setAvailable(true)
            ->setCategory('STARTER')
            ->setDescription('This is a test menu item added through a cart test')
            ->setImg('IMMMM.jpg')
            ->setName('TEST MENU')
            ->setPrice(11.1);

        $this->security->method('getUser')->willReturn($user);

        $order = new Order();
        $order->setUser($user)
            ->setOrderDate(new \DateTimeImmutable())
            ->setOrderStatus("INCART")
            ->setPaymentStatus("PROCESSING");

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Order::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->cartService->addItemToCart($menuItem, 5);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals(1, count($result->getOrderItems()));
        $this->assertEquals(55.5, $result->getTotalAmount());
    }

    public function testCalculateCartTotal()
    {
        $order = new Order();
        $menuItem = new MenuItem();
        $menuItem->setPrice(10.0);

        $orderItem = new OrderItem();
        $orderItem->setMenuItem($menuItem);
        $orderItem->setQuantity(3);
        $orderItem->setItemPrice(10.0);

        $order->addOrderItem($orderItem);

        $total = $this->cartService->calculateCartTotal($order);

        $this->assertEquals(30.0, $total);
    }

    public function testGetCartSummary()
    {
        $summary = $this->cartService->getCartSummary();

        $this->assertIsArray($summary);
        $this->assertArrayHasKey('totalQuantity', $summary);
        $this->assertArrayHasKey('totalAmount', $summary);
        $this->assertEquals("222", $summary['totalQuantity']);
        $this->assertEquals("333", $summary['totalAmount']);
    }
}