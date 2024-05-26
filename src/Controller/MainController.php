<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ItemsCollection;
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
        $itemCollectionData = [];
        $lastFiveItems = [];

        foreach ($items as $item) {
            $itemCollection = $this->entityManager->getRepository(ItemsCollection::class)->find($item->getItemCollection()->getId());
            $itemCollectionData['id'] = $item->getId();
            $itemCollectionData['author'] = $itemCollection->getUser()->getEmail();
            $itemCollectionData['collection'] = $itemCollection->getName();
            $itemCollectionData['item'] = $item->getName();
            $lastFiveItems[] = $itemCollectionData;
        }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'items' => $lastFiveItems,
        ]);
    }
}
