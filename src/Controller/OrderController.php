<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{

    #[Route('/api/payment', name: 'app_payment')]
    public function payment(): Response
    {


        return $this->json(200);
    }
    #[Route('/api/makeorder', name: 'app_make_order')]
    public function makeorder(CartService $cartService, EntityManagerInterface $manager): Response
    {

        if(!$this->getUser()){
            return $this->redirectToRoute('app_home');
        }

        $order = new Order();
        $order->setProfile($this->getUser()->getProfile());
        $order->setTotal($cartService->getTotal());

        foreach ($cartService->getCart() as $item){

            $orderItem = new OrderItem();
            $orderItem->setProduct($item['product']);
            $orderItem->setQuantity($item['quantity']);
            $orderItem->setOfOrder($order);

            $manager->persist($orderItem);

        }

        $manager->flush();
        $cartService->emptyCart();


        $this->addFlash('success', 'order confirmed');


        return $this->json(200);
    }

}
