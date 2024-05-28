<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Item;
use App\Entity\ItemsCollection;
use App\Entity\ItemTag;
use App\Form\CommentType;
use App\Form\ItemEditType;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use App\Repository\ItemsCollectionRepository;
use App\Repository\ItemTagRepository;
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $collection = $repository->find($request->get('id'));

        $items = $repository->findBy(['itemCollection' => $collection]);

        $pagination = $this->paginator->paginate(
            $items,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit',  20)
        );

        return $this->render('item/table.html.twig', [
            'controller_name' => 'ItemController',
            'pagination' => $pagination,
        ]);
    }


    #[Route('/{id}', name: 'app_item_id',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showItem(Request $request, Item $item, CommentService $commentService): Response
    {

        $itemName = $item->getName();
        $attributes = [];

        $items = $this->entityManager->getRepository(Item::class)->findItemAttributes($item->getId());

        foreach ($items->getItemAttributeValue() as $attributeValue) {
            $attribute['attribute'] = $attributeValue->getCustomItemAttribute()->getName();
            $attribute['value'] = $attributeValue->getName();
            $attributes[] = $attribute;
        }

        $comments = $this->entityManager->getRepository(Comments::class)->findCommentsByItemId($item->getId());


        $comment = $commentService->initializeComment($this->getUser(), $item);

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        }

        return $this->render('item/show.html.twig', [
            'controller_name' => 'ItemController',
            'attributes' => $attributes,
            'name' => $itemName,
            'comment_form' => $commentForm->createView(),
//            'tags' => $tagsArr,
            'comments' => $comments,
        ]);
    }


}