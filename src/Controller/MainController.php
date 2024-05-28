<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ItemsCollection;
use App\Entity\ItemTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        $items = $this->entityManager->getRepository(Item::class)->findLastFive();

        $biggestCollection = $this->entityManager->getRepository(ItemsCollection::class)->findTopFiveCollections();


        $tags = $this->entityManager->getRepository(ItemTag::class)->findAll();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'items' => $items,
            'collections' => $biggestCollection,
            'tags' => $tags,
        ]);
    }
}
