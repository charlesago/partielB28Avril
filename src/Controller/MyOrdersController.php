<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
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
    public function show(Order $order): Response
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


}