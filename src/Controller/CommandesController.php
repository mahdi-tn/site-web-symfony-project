<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Form\CommandesType;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Psr\Log\LoggerInterface;


#[Route('/commandes')]
class CommandesController extends AbstractController
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }



    #[Route('/cart', name: 'app_commandes_cart', methods: ['GET'])]
    public function cart(): Response
    {
        return $this->render('shoping-cart.html.twig');
    }

    #[Route('/', name: 'app_commandes_index', methods: ['GET'])]
    public function index(CommandesRepository $commandesRepository): Response
    {
        return $this->render('commandes/index.html.twig', [
            'commandes' => $commandesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commandes_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        // Log the start of the code snippet
        $logger->info('Start of code snippet');

        // Log the creation of a new Commandes entity
        $logger->info('Creating new Commandes entity');
        $commande = new Commandes();
        // Log the creation of the form
        $logger->info('Creating form');
        $form = $this->createForm(CommandesType::class, $commande);

        $formData = $form->getData();

        // Dump the form data
        dump($formData);
        die;
        $logger->error('Request formformform: {data}', ['data' => json_encode($form)]);

        // Log the handling of the form request
        $logger->info('Handling form request');
        $form->handleRequest($request);

        // Log the end of the code snippet
        $logger->info('End of code snippet');

        $rep = "pass";

        // Log the request data
        $requestData = $request->request->all();
        $logger->error('Request Data: {data}', ['data' => json_encode($requestData)]);

        if ($form->isSubmitted()) {
            // Log the form data and submission status
            $formData = $form->getData();
            $isSubmitted = $form->isSubmitted();
            $isValid = $form->isValid();
            $logger->info('Form submitted with data: {data}, submitted: {submitted}, valid: {valid}', [
                'data' => json_encode($formData),
                'submitted' => $isSubmitted,
                'valid' => $isValid,
            ]);

            if ($form->isValid()) {
                $entityManager->persist($commande);
                $entityManager->flush();
                $rep = "true";

                return $this->redirectToRoute('app_commandes_index', ['rep' => $rep], Response::HTTP_SEE_OTHER);
            } else {
                $rep = "submitted, not valid";

                // Log validation errors
                $errors = $form->getErrors(true);
                foreach ($errors as $error) {
                    $logger->error('Validation Error: {error}', ['error' => $error->getMessage()]);
                }
            }
        } else {
            $rep = "not submitted";
            // Log that the form was not submitted
            $logger->info('Form not submitted');
            // Handle the case where the form is not submitted
        }

        return $this->redirectToRoute('app_commandes_index', ['rep' => $rep]);
    }

    #[Route('/{id}', name: 'app_commandes_show', methods: ['GET'])]
    public function show(Commandes $commande): Response
    {
        return $this->render('commandes/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commandes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commandes $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandesType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commandes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commandes/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commandes_delete', methods: ['POST'])]
    public function delete(Request $request, Commandes $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commandes_index', [], Response::HTTP_SEE_OTHER);
    }
}
