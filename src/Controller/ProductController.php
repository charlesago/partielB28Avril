<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class ProductController extends AbstractController
{
    #[Route('/show', name: 'app_product')]
    public function index(ProductRepository $repository): Response
    {

        return $this->render('product/index.html.twig', [
            'products' => $repository->findAll(),
            ]);
    }

    #[Route('admin/delete/{id}', name: 'delete_product')]
    public function delete(Product $product, EntityManagerInterface $manager): Response
    {
        if ($product) {
            $manager->remove($product);
            $manager->flush();
        }
        return $this->redirectToRoute('app_product');

    }
    #[Route('/admin/create/', name: 'create_product')]
    #[Route('admin/update/{id}', name: 'update_product')]
    public function create(Product $product = null, Request $request, EntityManagerInterface $manager,): response
    {
        $edit = false;

        if ($product) {
            $edit = true;
        }
        if (!$edit) {
            $product = new Product();
        }

        $formProduct = $this->createForm(ProductType::class, $product);
        $formProduct->handleRequest($request);
        if ($formProduct->isSubmitted() && $formProduct->isValid()) {

            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('app_product');
        }
        return $this->render('product/create.html.twig', [
            'formProduct' => $formProduct
        ]);

    }


}
