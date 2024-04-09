<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/my/orders')]

class MyOrdersController extends AbstractController
{
    #[Route('/', name: 'app_my_orders_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json(200);
    }



        #[Route('/{id}', name: 'app_my_orders_show', methods: ['GET'])]
        public function show(Order $order, OrderService $orderService): Response
        {
            if($this->getUser()->getProfile() !== $order->getProfile())
            {
                $this->addFlash('warning',
                    'mind your own orders');
                return $this->json(200);
            }
            $orders = $order;
            return $this->json($orders, 200, [],['groups' => 'myorders']);
        }
    #[Route('/get', name: 'app_get_my_order')]
    public function getMyOrder( OrderRepository $repository): Response
    {
        $myOrder = $repository->findBy(['profile' => $this->getUser()->getProfile() ]);
        $returnable = [];
        foreach ($myOrder as $order) {
            $items = [];
            foreach ($order->getOrderItems() as $item){
                $items[] = [
                    'id'=>$item->getId(),
                    'quantity'=>$item->getQuantity(),
                    'productName'=>$item->getProduct()->getName(),
                    'productId'=>$item->getProduct()->getId(),
                    'productPrice'=>$item->getProduct()->getPrice(),
                ];
            }
            $returnable[] = [
                'id' => $order->getId(),
                'profile' => $order->getProfile(),
                'total' => $order->getTotal(),
                'products' => $items,
            ];
        }
        return $this->json($returnable, Response::HTTP_OK, [], ['groups' => ['myorders']]);
    }


}