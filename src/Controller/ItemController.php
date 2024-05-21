<?php

namespace App\Controller;

use App\Entity\CustomItemAttribute;
use App\Entity\Item;


use App\Entity\ItemsCollection;
use App\Entity\ItemTag;
use App\Form\ItemType;
use App\Repository\ItemTagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function create(Request $request, EntityManagerInterface $entityManager, ItemTagRepository $itemTagRepository): Response
    {
        $item = new Item();
        $this->denyAccessUnlessGranted('ROLE_USER');
        $itemTag = $itemTagRepository->findAll();
        $itemCollectionRepository = $entityManager->getRepository(ItemsCollection::class);
        $itemCollection = $itemCollectionRepository->find(19);
        $itemCollectionName = $itemCollection->getName();
        $item->setItemCollection($itemCollection);

        $form = $this->createForm(ItemType::class, $item,
        [
            'itemTags' => $itemTag,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }

        return $this->render('item/form.html.twig', [
            'item_form' => $form->createView(),
            'item_name' => $itemCollectionName,
        ]);
    }

    #[Route('/item/{id}/update', name: 'app_item_update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function update(Request $request, Item $collection): Response
    {
        $itemName = $collection->getItemCollection()->getName();
        $form = $this->createForm(ItemType::class, $collection);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Collection successfully updated');
        }

        return $this->render('item/form.html.twig', [
            'item_form' => $form->createView(),
            'item_name' => $itemName,
        ]);
    }

    #[Route('/item/show', name: 'app_item_show',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function show(Item $item): Response
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
                    $itemsArray['author'] = $item->getItemCollection()->getUser()->getEmail();

                    $listOfItems[] = $itemsArray;
                }
            }

        return $this->render('item/show.html.twig', [
            'controller_name' => 'ItemController',
            'items' => $listOfItems,
        ]);
    }

    #[Route('/item/collection/{id}', name: 'app_item_collection',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showCollectionItems(Request $request, ItemsCollection $itemsCollection): Response
    {
        $user = $this->security->getUser();
        $itemCollectionName = $itemsCollection->getName();
        $items = $itemsCollection->getItems();
        $listOfItems = [];
        $attributes = [];


        foreach ($items as $item) {
            $itemsValue['id'] = $item->getId();
            $itemsValue['name'] = $item->getName();
            $itemsValue['author'] = $item->getItemCollection()->getUser()->getEmail();
            $itemsValue['collection'] = $item->getItemCollection()->getName();
            $itemsAttributes = [];
            $itemsAttributesValue = [];

            foreach ($item->getItemAttributeValue() as $attributeValue) {
                $attributeName = $attributeValue->getName();
                $attribute = $attributeValue->getCustomItemAttribute()->getName();
                $itemsAttributesValue[] = $attributeName;
                $itemsAttributes[] = $attribute;
            }
            $itemsValue['attributes'] = $itemsAttributes;
            $itemsValue['values'] = $itemsAttributesValue;
            $listOfItems[] = $itemsValue;
        }

        return $this->render('item/table.html.twig', [
            'controller_name' => 'ItemController',
            'items' => $listOfItems,
            'collection' => $itemCollectionName,
        ]);
    }


    #[Route('/item/{id}', name: 'app_item_id',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showItem(Request $request, Item $item): Response
    {
//        $itemRepository = $this->entityManager->getRepository(Item::class);
//        $value = $request->attributes->get('id');
//        $item = $itemRepository->find($value);
        $itemName = $item->getName();
        $itemAttribute = $item->getItemAttribute();
        $itemAttributeValue = $itemAttribute->getValue();

        $itemAttributeValues = [$itemAttribute, $itemAttributeValue];

        return $this->render('item/show.html.twig', [
            'controller_name' => 'ItemController',
        ]);
    }

        #[Route('/autocomplete/tags', name: 'autocomplete_tags',  methods: [Request::METHOD_GET])]
        public function getTags(Request $request): JsonResponse
        {
            $query = $request->query->get('query');
            $tags = $this->entityManager->getRepository(ItemTag::class)
                ->createQueryBuilder('t')
                ->where('LOWER(t.name) LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $query . '%')
                ->getQuery()
                ->getResult();

            $tagList = [];

            foreach ($tags as $tag) {
                $tagList[] = [
                    'value' => (int) $tag->getId(),
                    'text' => (string) $tag->getName(),
                ];
            }
            return new JsonResponse(['results'=>$tagList]);
        }
}