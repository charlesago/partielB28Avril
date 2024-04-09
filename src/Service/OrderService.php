<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class OrderService
{
    public function getMyOrder(OrderRepository $repository): Response
    {
        $myOrder = $repository->findBy(['profile' => $this->getUser()->getProfile()]);
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