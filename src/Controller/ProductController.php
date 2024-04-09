<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\QrCodeGeneratorService;
use Twig\Environment;


class ProductController extends AbstractController
{
    #[Route('/api/show', name: 'app_product')]
    public function index(ProductRepository $repository): Response
    {
        $products = $repository->findAll();

        return $this->json($products, 200, [],  ['groups' => 'show_product']);
    }
    #[Route('/show', name: 'app_product')]
    public function show(ProductRepository $repository): Response
    {
        $products = $repository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products
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
    public function create(Product $product = null, Request $request, EntityManagerInterface $manager,QrCodeGeneratorService $qrCodeGeneratorService): response
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
            $qrcode = $qrCodeGeneratorService->createQrCode($product->getName());
            $product->setQrcode($qrcode);

            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('app_product');
        }
        return $this->render('product/create.html.twig', [
            'formProduct' => $formProduct
        ]);

    }
    #[Route('/findby/qrcode/{name}', name: 'match_qrcode_toproduct', methods: ['GET'])]
    public function matchQrCodeToProduct(
        ProductRepository $productRepository,
        Product $product): Response
    {
        $matchingProduct = $productRepository->findOneBy(['name' => $product->getName()]);
        return $this->json($matchingProduct, 200);
    }

    #[Route('admin/product/{name}', name: 'app_admin_product_qrcode')]
    public function displayQrCode(ProductRepository $repository, Product $product, Pdf $pdf)
    {
        $linkedProduct = $repository->findOneBy(['name'=>$product->getName()]);
        $qr =  $this->renderView('product/qr.html.twig', [
            "product"=>$linkedProduct,
            'qr'=>$linkedProduct->getQrCode()
        ]);
        return new PdfResponse(
            $pdf->getOutputFromHtml($qr),
            $linkedProduct->getName()
        );
    }

}
