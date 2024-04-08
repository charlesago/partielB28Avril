<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/my/orders')]
class MyOrdersController extends AbstractController
{
    #[Route('/', name: 'app_my_orders_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('my_orders/index.html.twig', [
        ]);
    }



    #[Route('/{id}', name: 'app_my_orders_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        if($this->getUser()->getProfile() !== $order->getProfile())
        {
            $this->addFlash('warning',
                'mind your own orders');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('my_orders/show.html.twig', [
            'order' => $order,
        ]);
    }


}