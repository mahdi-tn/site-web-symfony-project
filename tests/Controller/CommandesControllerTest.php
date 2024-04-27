<?php

namespace App\Test\Controller;

use App\Entity\Commandes;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandesControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/commandes/controller2/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Commandes::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Commande index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'commande[userEmail]' => 'Testing',
            'commande[productRefs]' => 'Testing',
            'commande[prix]' => 'Testing',
            'commande[prix_total]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commandes();
        $fixture->setUserEmail('My Title');
        $fixture->setProductRefs('My Title');
        $fixture->setPrix('My Title');
        $fixture->setPrix_total('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Commande');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commandes();
        $fixture->setUserEmail('Value');
        $fixture->setProductRefs('Value');
        $fixture->setPrix('Value');
        $fixture->setPrix_total('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'commande[userEmail]' => 'Something New',
            'commande[productRefs]' => 'Something New',
            'commande[prix]' => 'Something New',
            'commande[prix_total]' => 'Something New',
        ]);

        self::assertResponseRedirects('/commandes/controller2/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getUserEmail());
        self::assertSame('Something New', $fixture[0]->getProductRefs());
        self::assertSame('Something New', $fixture[0]->getPrix());
        self::assertSame('Something New', $fixture[0]->getPrix_total());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commandes();
        $fixture->setUserEmail('Value');
        $fixture->setProductRefs('Value');
        $fixture->setPrix('Value');
        $fixture->setPrix_total('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/commandes/controller2/');
        self::assertSame(0, $this->repository->count([]));
    }
}
