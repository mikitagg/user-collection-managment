<?php

namespace App\Controller;

use App\Entity\CustomItemAttribute;
use App\Entity\Item;

use App\Entity\ItemAttributeValue;
use App\Entity\ItemsCollection;
use App\Form\CollectionType;
use App\Form\ItemAttributeValueType;
use App\Form\ItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class ItemController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    )
    {
    }

    #[Route('/item', name: 'app_item')]
    public function index(): Response
    {
        return $this->render('item/index.html.twig', [
            'controller_name' => 'ItemController',
        ]);
    }

    #[Route('/item/create', name: 'item_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $item = new Item();
        $this->denyAccessUnlessGranted('ROLE_USER');

        $itemCollectionRepository = $entityManager->getRepository(ItemsCollection::class);
        $itemCollection = $itemCollectionRepository->find(19);
        $itemCollectionName = $itemCollection->getName();
        $item->setItemCollection($itemCollection);

        $form = $this->createForm(ItemType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();

            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }

        return $this->render('item/form.html.twig', [
            'item_form' => $form,
            'item_name' => $itemCollectionName,
        ]);
    }

    #[Route('/item/{id}/update', name: 'app_item_update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function update(Request $request, ItemsCollection $collection): Response
    {
        $form = $this->createForm(ItemType::class, $collection);
        $form->handleRequest($request);

//        $currentUser = $this->getUser();

//        if ($collection->getUser() !== $currentUser) {
//            throw $this->createAccessDeniedException('You are not allowed to access this collection.');
//        }

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Collection successfully updated');
        }

        return $this->render('item/form.html.twig', [
            'action' => 'update',
            'form' => $form->createView(),
            'collection' => $collection
        ]);
    }

    #[Route('/item/show', name: 'app_item_show',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function show(): Response
    {
        $user = $this->security->getUser();
        $itemCollectionRepository = $this->entityManager->getRepository(ItemsCollection::class);
        $itemCollection = $itemCollectionRepository->findByUser($user->getId());
        $itemsArray = [];
        $listOfItems = [];

            foreach ($itemCollection as $items) {
                foreach ($items->getItems() as $item) {

                    $itemsArray['id'] = $item->getId();
                    $itemsArray['name'] = $item->getName();
                    $listOfItems[] = $itemsArray;
                 //   dd($item->getName());
                }
            }



        return $this->render('item/show.html.twig', [
            'controller_name' => 'ItemController',
            'items' => $listOfItems,
        ]);
    }

}