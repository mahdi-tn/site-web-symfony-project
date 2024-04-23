<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Comments;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'app_products_index', methods: ['GET'])]
    public function index(ProductsRepository $productsRepository): Response
    {
        return $this->render('products/index.html.twig', [
            'products' => $productsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{p_ref}/show', name: 'app_products_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, string $p_ref): Response
    {
        $repository = $doctrine->getRepository(Products::class);
        $product = $repository->find($p_ref);

        $commentsRepository = $doctrine->getRepository(Comments::class);
        $comments = $commentsRepository->findBy(['product' => $product]);

        return $this->render('products/show.html.twig', [
            'product' => $product,
            'comments' => $comments,
        ]);
    }

    #[Route('/{p_ref}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ManagerRegistry $doctrine, string $p_ref): Response
    {
        $repository = $doctrine->getRepository(Products::class);
        $product = $repository->find($p_ref);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('products/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{p_ref}/del', name: 'app_products_delete', methods: ['POST'])]
    public function delete(Request $request, ManagerRegistry $doctrine, string $p_ref): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Products::class)->find($p_ref);

        if ($product && $this->isCsrfTokenValid('delete' . $product->getPRef(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/search', name: 'app_products_display', methods: ['GET'])]
    public function display(Request $request, ManagerRegistry $doctrine): Response
    {
        $UsrInput = $request->query->get('UsrInput');

        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(Products::class);

        $queryBuilder = $repository->createQueryBuilder('p')
            ->where('p.p_name = :UsrInput OR p.p_category = :UsrInput')
            ->setParameter('UsrInput', $UsrInput);

        $products = $queryBuilder->getQuery()->getResult();

        if (!$products) {
            $products = [];
        }

        return $this->render('products/index.html.twig', [
            'products' => $products
        ]);
    }
}
