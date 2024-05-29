<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Item;
use App\Entity\ItemsCollection;
use App\Form\CommentType;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('/item')]
class ItemController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly PaginatorInterface $paginator
    )
    {
    }

    #[Route('/{id}/create', name: 'item_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->get('id');
        $item = new Item();
        $item->setItemCollection($entityManager->getRepository(ItemsCollection::class)->findWithCustomAttributes($id));
        $form = $this->createForm(ItemType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $this->entityManager->persist($item);
            $this->entityManager->flush();


        }

        return $this->render('item/form.html.twig', [
            'item_form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update', name: 'app_item_update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function update(Request $request, Item $item): Response
    {
        $collectionName = $item->getItemCollection()->getName();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Collection successfully updated');
        }

        return $this->render('item/form.html.twig', [
            'item_form' => $form->createView(),
            'item_name' => $collectionName,
        ]);
    }

    #[Route('/collection/{id}', name: 'app_item_collection',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showCollectionItems(Request $request, ItemRepository $repository): Response
    {

        $collectionId = $request->get('id');
        $items = $repository->findBy(['itemCollection' => $collectionId]);

        $pagination = $this->paginator->paginate(
            $items,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit',  20)
        );

        return $this->render('item/table.html.twig', [
            'controller_name' => 'ItemController',
            'pagination' => $pagination,
            'collection' => $collectionId
        ]);
    }


    #[Route('/{id}', name: 'app_item_id',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showItem(Request $request, Item $item, CommentService $commentService): Response
    {
        $items = $this->entityManager->getRepository(Item::class)->findItemAttributes($item->getId());

        $comments = $commentService->initializeComment($this->getUser(), $item);

        $commentForm = $this->createForm(CommentType::class, $comments);
        $commentForm->handleRequest($request);

        if($commentForm->isSubmitted() && $commentForm->isValid()) {

            $this->entityManager->persist($comments);
            $this->entityManager->flush();
        }

        return $this->render('item/show.html.twig', [
            'controller_name' => 'ItemController',
            'items' => $items,
            'comment_form' => $commentForm->createView(),
        ]);
    }

    #[Route('/{id}/comments', name: 'app_item_comments',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function comments(Item $item): JsonResponse
    {
        $comments = $this->entityManager->getRepository(Comments::class)->findBy(['item' => $item->getId()]);

        $responseData = array_map(function ($comment) {
            return [
                'id' => $comment['id'],
                'content' => $comment['content'],
                'username' => $comment['username'],
            ];
        }, $comments);

        return new JsonResponse($responseData);
    }

    #[Route('/{id}/delete', name: 'app_item_delete', methods: ['GET', 'POST'])]
    public function delete( EntityManagerInterface $entityManager, Item $item): Response
    {

        $entityManager->remove($item);
        $entityManager->flush();

        return $this->redirectToRoute('app_item_collection', ['id' => $item->getItemCollection()->getId()]);
    }



}