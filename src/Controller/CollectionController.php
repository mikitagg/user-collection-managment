<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\ItemsCollection;
use App\Form\CollectionType;
use App\Repository\ItemsCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CollectionController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    )
    {
    }


    #[Route('/collection', name: 'app_collection')]
    public function index(): Response
    {

        return $this->render('collection/index.html.twig', [
            'controller_name' => 'CollectionController'
        ]);
    }

    #[Route('/collection/create', name: 'app_collection_create')]
    public function create(Request $request): Response
    {
        $itemsCollection = new ItemsCollection();
        $itemsCollection->setUser($this->getUser());
        $form = $this->createForm(CollectionType::class, $itemsCollection);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $collection = $form->getData();

            $this->entityManager->persist($collection);
            $this->entityManager->flush();

            $this->addFlash('success', 'Collection created.');
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),
        ]);

    }

    #[Route('/collection/{id}/update', name: 'app_collection_update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function update(Request $request, ItemsCollection $collection): Response
    {
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Collection successfully updated');
        }

        return $this->render('collection/form.html.twig', [
            'action' => 'update',
            'form' => $form->createView(),
            'collection' => $collection
        ]);
    }

    #[Route('/collection/user', name: 'app_collection_user', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function show(Request $request, ItemsCollectionRepository $itemsCollectionRepository): Response
    {
        $user = $this->getUser();
        $collection = [];
        $itemCollection = $itemsCollectionRepository->findByUser($user->getId());

        $itemsCollection = $this->entityManager->getRepository(ItemsCollection::class)->findAll();

        foreach ($itemCollection as $itemsCollection) {
            $collectionValues['id'] = $itemsCollection->getId();
            $collectionValues['name'] = $itemsCollection->getName();
            $collectionValues['description'] = $itemsCollection->getDescription();
            $attributes = $itemsCollection->getCustomItemAttributes();
            $customAttribute = [];

            foreach ($attributes as $attribute) {
                $customAttribute[] = $attribute->getName();
            }
            $collectionValues['attributes'] = $customAttribute;

            $collection[] = $collectionValues;

        }

        return $this->render('collection/table.html.twig', [
            'action' => 'create',
            'collection' => $collection,
        ]);
    }


    #[Route('/collection/show', name: 'app_collection_show', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function userCollection(Request $request): Response
    {
        $user = $this->getUser();

        $collection = [];
        $attributeValues = [];
        $itemCollectionRepository = $this->entityManager->getRepository(ItemsCollection::class);
        $itemCollection = $itemCollectionRepository->findByUser($user->getId());

        foreach ($itemCollection as $itemsCollection) {
            $collection['name'] = $itemsCollection->getName();
            $collection['id'] = $itemsCollection->getId();
            $attributeValues[] = $collection;
        }

        return $this->render('collection/show.html.twig', [
            'action' => 'create',
            'attributeValues' => $attributeValues,
        ]);
    }
}
