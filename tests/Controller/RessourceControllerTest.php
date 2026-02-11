<?php

namespace App\Tests\Controller;

use App\Entity\Ressource;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RessourceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $ressourceRepository;
    private string $path = '/ressource/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->ressourceRepository = $this->manager->getRepository(Ressource::class);

        foreach ($this->ressourceRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ressource index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ressource[titre]' => 'Testing',
            'ressource[description]' => 'Testing',
            'ressource[auteur]' => 'Testing',
            'ressource[urlFichier]' => 'Testing',
            'ressource[typeFichier]' => 'Testing',
            'ressource[imageCouverture]' => 'Testing',
            'ressource[tags]' => 'Testing',
            'ressource[tailleFichier]' => 'Testing',
            'ressource[nombreVues]' => 'Testing',
            'ressource[nombreTelechargements]' => 'Testing',
            'ressource[noteMoyenne]' => 'Testing',
            'ressource[statut]' => 'Testing',
            'ressource[dateAjout]' => 'Testing',
            'ressource[estActive]' => 'Testing',
            'ressource[typeRessource]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->ressourceRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ressource();
        $fixture->setTitre('My Title');
        $fixture->setDescription('My Title');
        $fixture->setAuteur('My Title');
        $fixture->setUrlFichier('My Title');
        $fixture->setTypeFichier('My Title');
        $fixture->setImageCouverture('My Title');
        $fixture->setTags('My Title');
        $fixture->setTailleFichier('My Title');
        $fixture->setNombreVues('My Title');
        $fixture->setNombreTelechargements('My Title');
        $fixture->setNoteMoyenne('My Title');
        $fixture->setStatut('My Title');
        $fixture->setDateAjout('My Title');
        $fixture->setEstActive('My Title');
        $fixture->setTypeRessource('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ressource');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ressource();
        $fixture->setTitre('Value');
        $fixture->setDescription('Value');
        $fixture->setAuteur('Value');
        $fixture->setUrlFichier('Value');
        $fixture->setTypeFichier('Value');
        $fixture->setImageCouverture('Value');
        $fixture->setTags('Value');
        $fixture->setTailleFichier('Value');
        $fixture->setNombreVues('Value');
        $fixture->setNombreTelechargements('Value');
        $fixture->setNoteMoyenne('Value');
        $fixture->setStatut('Value');
        $fixture->setDateAjout('Value');
        $fixture->setEstActive('Value');
        $fixture->setTypeRessource('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ressource[titre]' => 'Something New',
            'ressource[description]' => 'Something New',
            'ressource[auteur]' => 'Something New',
            'ressource[urlFichier]' => 'Something New',
            'ressource[typeFichier]' => 'Something New',
            'ressource[imageCouverture]' => 'Something New',
            'ressource[tags]' => 'Something New',
            'ressource[tailleFichier]' => 'Something New',
            'ressource[nombreVues]' => 'Something New',
            'ressource[nombreTelechargements]' => 'Something New',
            'ressource[noteMoyenne]' => 'Something New',
            'ressource[statut]' => 'Something New',
            'ressource[dateAjout]' => 'Something New',
            'ressource[estActive]' => 'Something New',
            'ressource[typeRessource]' => 'Something New',
        ]);

        self::assertResponseRedirects('/ressource/');

        $fixture = $this->ressourceRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getAuteur());
        self::assertSame('Something New', $fixture[0]->getUrlFichier());
        self::assertSame('Something New', $fixture[0]->getTypeFichier());
        self::assertSame('Something New', $fixture[0]->getImageCouverture());
        self::assertSame('Something New', $fixture[0]->getTags());
        self::assertSame('Something New', $fixture[0]->getTailleFichier());
        self::assertSame('Something New', $fixture[0]->getNombreVues());
        self::assertSame('Something New', $fixture[0]->getNombreTelechargements());
        self::assertSame('Something New', $fixture[0]->getNoteMoyenne());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getDateAjout());
        self::assertSame('Something New', $fixture[0]->getEstActive());
        self::assertSame('Something New', $fixture[0]->getTypeRessource());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ressource();
        $fixture->setTitre('Value');
        $fixture->setDescription('Value');
        $fixture->setAuteur('Value');
        $fixture->setUrlFichier('Value');
        $fixture->setTypeFichier('Value');
        $fixture->setImageCouverture('Value');
        $fixture->setTags('Value');
        $fixture->setTailleFichier('Value');
        $fixture->setNombreVues('Value');
        $fixture->setNombreTelechargements('Value');
        $fixture->setNoteMoyenne('Value');
        $fixture->setStatut('Value');
        $fixture->setDateAjout('Value');
        $fixture->setEstActive('Value');
        $fixture->setTypeRessource('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ressource/');
        self::assertSame(0, $this->ressourceRepository->count([]));
    }
}
