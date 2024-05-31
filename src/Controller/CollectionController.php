<?php

namespace App\Controller;

use App\Entity\ItemsCollection;
use App\Form\CollectionType;
use App\Repository\ItemsCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/collection')]
class CollectionController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PaginatorInterface $paginator
    )
    {
    }

    #[Route('/create', name: 'app_collection_create')]
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
            return $this->redirectToRoute('app_collection_show_user', ['id' => $collection->getUser()->getId()]);
        }
        return $this->render('collection/form.html.twig', [
            'action' => 'create',
            'form' => $form->createView(),
        ]);

    }

    #[Route('/{id}/update', name: 'app_collection_update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted('edit', 'collection', 'You have no permission to update this collection', 404)]
    public function update(Request $request, ItemsCollection $collection): Response
    {
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_collection_show_user', ['id' => $collection->getUser()->getId()]);
        }
        return $this->render('collection/form.html.twig', [
            'action' => 'update',
            'form' => $form->createView(),
            'collection' => $collection
        ]);
    }

    #[Route('/show/all', name: 'app_collection_show_all', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showAllCollections(Request $request, ItemsCollectionRepository $itemsCollectionRepository): Response
    {
        $query = $itemsCollectionRepository->getItemCollectionWithAuthorAndCategory();
        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit',  20)
        );
        return $this->render('collection/showall.html.twig', [
            'action' => 'create',
            'pagination' => $pagination,

        ]);
    }

    #[Route('/show/user/{id}/', name: 'app_collection_show_user', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showUserCollections(Request $request, ItemsCollectionRepository $itemsCollectionRepository): Response
    {
        $user_id = $request->get('id');
        $userItemCollections = $itemsCollectionRepository->getItemCollectionWithCategories($user_id);
        $pagination = $this->paginator->paginate(
            $userItemCollections,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit',  20)
        );
        return $this->render('collection/show_user_collection.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_collection_delete', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'collection', 'You have no permission to delete this collection', 404)]
    public function delete(Request $request, EntityManagerInterface $entityManager, ItemsCollection $collection): Response
    {
        $entityManager->remove($collection);
        $entityManager->flush();
        return $this->redirectToRoute('app_collection_show_user', ['id' => $this->getUser()->getId()]);
    }

}
