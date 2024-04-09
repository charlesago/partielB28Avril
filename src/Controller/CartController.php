<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CartController extends AbstractController
{
    #[Route('/api/cart', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {

        $response = [
            $cart = $cartService->getCart(),
            $total = $cartService->getTotal()
        ];

        return $this->json($response[], 200, [],  ['groups' => 'show_product']);
    }

    #[Route('/api/cart/add/{id}/{quantity}', name: 'app_cart_add')]
    #[Route('/api/cart/addfromcart/{id}/{quantity}', name: 'app_cart_add_from_cart')]
    public function add(Request $request,Product $product, $quantity, CartService $cartService): Response
    {
        $cartService->addProduct($product, $quantity);
        $routeName = $request->attributes->get("_route");

        $redirection = 'app_product';

        if($routeName ==="app_cart_add_from_cart" ){
            $redirection ='app_cart';

        }

        return $this->json(200);
    }


    #[Route('/api/cart/removeone/{id}', name: 'app_cart_remove_one')]
    public function removeOne(CartService $cartService, Product $product): Response
    {
        $cartService->removeProduct($product);

        return $this->json(200);

    }

    #[Route('/api/cart/removewhole/{id}', name: 'app_cart_remove_whole')]
    public function removewhole(CartService $cartService, Product $product): Response
    {
        $cartService->removeProductRow($product);

        return $this->json(200);
    }

    #[Route('/api/cart/empty', name: 'app_cart_empty')]
    public function emptyCart(CartService $cartService): Response
    {
        $cartService->emptyCart();

        return $this->json(200);
    }
}