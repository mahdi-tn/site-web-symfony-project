<?php

namespace App\Controller;

use App\Form\Commandes1Type;
use App\Entity\Commandes;
use App\Form\CommandesType;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/commandes/controller2')]
class CommandesController2Controller extends AbstractController
{
    #[Route('/', name: 'app_commandes_controller2_index', methods: ['GET'])]
    public function index(CommandesRepository $commandesRepository): Response
    {
        return $this->render('commandes_controller2/index.html.twig', [
            'commandes' => $commandesRepository->findAll(),
        ]);
    }

    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/new', name: 'app_commandes_new2', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager, LoggerInterface $logger): Response
    {
        try {
            // Retrieve the CSRF token from the request
            $submittedToken = $request->headers->get('X-CSRF-Token');
            $formToken = $csrfTokenManager->getToken('form_intention')->getValue();

            // Log the generated CSRF token for debugging
            $logger->info('Generated CSRF Token rrrrrrrrrrrr: ' . $formToken);
            // Verify the CSRF token
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('form_intention', $formToken))) {
                throw new InvalidCsrfTokenException('Invalid CSRF token');
            }

            $data = json_decode($request->getContent(), true);

            $commande = new Commandes();
            $form = $this->createForm(CommandesType::class, $commande);
            // $form->handleRequest($request);
            $form->submit($data);

            // if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isSubmitted()) {
                $entityManager->persist($commande);
                $entityManager->flush();

                return $this->redirectToRoute('app_commandes_index', [], Response::HTTP_SEE_OTHER);
            } else {

                // Handle form submission errors or invalid form data here
                $this->logger->info('Form aaaaaaaaaaaaaaa:', ['data' => $data]);
                $this->logger->info('Form aaaaaaaaaaaaaaa:', ['data' => $request->getContent()]);
                $errors = $form->getErrors(true);
                foreach ($errors as $error) {
                    $this->logger->info('ERR-Form bbbbbbbbbbb:', ['data' => $request->getPayload()->get('_token')]);
                    $this->logger->info('ERR-Form bbbbbbbbbbb:', ['data' => $request->getPayload()->get('_csrf_token')]);
                    $this->logger->error($error->getMessage());
                }
                // $this->logger->info('Form aaaaaaaaaaaaaaa:', ['data' => "data"]);

                // check CSRF token values
                // $requestContent = $request->getContent();
                $requestData = json_decode($request->getContent(), true);
                // $csrfToken = $requestData['_csrf_token'];
                // $this->logger->info('CSRF ccccccccccccccc Token: ' . json_encode($csrfToken));

                // if ($csrfToken !== $expectedCsrfToken) {
                //     throw new \Exception(sprintf("The CSRF token is invalid. Expected: %s, got: %s", $expectedCsrfToken, $csrfToken));
                // }

                $this->logger->info('Form aaaaaaaaaaaaaaa:', ['data' => $data]);
            }

            // Handle any other potential errors here
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_commandes_index', ['rep' => $e]);
            // You can also return a specific response based on the exception
            return $this->redirectToRoute('error_route_name');
        }
        // $expectedToken = $csrfTokenManager->getToken('app_commandes_new2')->getValue();
        // $this->logger->info('ERR ccccccccccccccc ');
        // $this->logger->error($expectedToken);

        $requestData = json_encode($request);
        $this->logger->info('Request data ccccccccccccccc: ' . json_encode($request->request->all()));
        dump($request);
        die;
        return $this->redirectToRoute('app_commandes_index', ['rep' => 'not pass']);
    }



    #[Route('/{id}', name: 'app_commandes_controller2_show', methods: ['GET'])]
    public function show(Commandes $commande): Response
    {
        return $this->render('commandes_controller2/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commandes_controller2_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commandes $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Commandes1Type::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commandes_controller2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commandes_controller2/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commandes_controller2_delete', methods: ['POST'])]
    public function delete(Request $request, Commandes $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commandes_controller2_index', [], Response::HTTP_SEE_OTHER);
    }
}
