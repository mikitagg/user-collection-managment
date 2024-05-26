<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Item;
use App\Entity\ItemsCollection;
use App\Form\CommentType;
use App\Form\ItemEditType;
use App\Form\ItemType;
use App\Repository\ItemTagRepository;
use App\Service\CommentService;
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

    #[Route('/item/create', name: 'item_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, ItemTagRepository $itemTagRepository): Response
    {
        $item = new Item();
        $this->denyAccessUnlessGranted('ROLE_USER');
        $item->setItemCollection($entityManager->getRepository(ItemsCollection::class)->findWithCustomAttributes(19));


        $form = $this->createForm(ItemType::class, $item,
        );

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

    #[Route('/item/{id}/update', name: 'app_item_update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function update(Request $request, Item $item): Response
    {
        $collectionName = $item->getItemCollection()->getName();
        $form = $this->createForm(ItemEditType::class, $item);
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

    #[Route('/item/collection/{id}', name: 'app_item_collection',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showCollectionItems(Request $request, ItemsCollection $itemsCollection): Response
    {
        $user = $this->security->getUser();
        $itemCollectionName = $itemsCollection->getName();
        $items = $itemsCollection->getItems();
        $listOfItems = [];


        foreach ($items as $item) {
            $itemsValue['id'] = $item->getId();
            $itemsValue['name'] = $item->getName();
            $itemsValue['author'] = $item->getItemCollection()->getUser()->getEmail();
            $itemsValue['collection'] = $item->getItemCollection()->getName();
//            $itemsAttributes = [];
//            $itemsAttributesValue = [];
//
//            foreach ($item->getItemAttributeValue() as $attributeValue) {
//                $attributeName = $attributeValue->getName();
//                $attribute = $attributeValue->getCustomItemAttribute()->getName();
//                $itemsAttributesValue[] = $attributeName;
//                $itemsAttributes[] = $attribute;
//            }
//            $itemsValue['attributes'] = $itemsAttributes;
//            $itemsValue['values'] = $itemsAttributesValue;
            $listOfItems[] = $itemsValue;
        }

        return $this->render('item/table.html.twig', [
            'controller_name' => 'ItemController',
            'items' => $listOfItems,
            'collection' => $itemCollectionName,
        ]);
    }


    #[Route('/item/{id}', name: 'app_item_id',  methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function showItem(Request $request, Item $item, CommentService $commentService): Response
    {

        $itemName = $item->getName();
        $commentsContent = [];
        $tagsArr = [];
        $attributes = [];

        $items = $this->entityManager->getRepository(Item::class)->findItemAttributes($item->getId());

        foreach ($items->getItemAttributeValue() as $attributeValue) {
            $attribute['attribute'] = $attributeValue->getCustomItemAttribute()->getName();
            $attribute['value'] = $attributeValue->getName();
            $attributes[] = $attribute;
        }

        $tags = $item->getItemTags();

        foreach ($tags as $tag) {
            $tagsArr[] = $tag->getName();
        }

        $comments = $this->entityManager->getRepository(Comments::class)->findCommentsByItemId($item->getId());

        foreach ($comments as $com)
        {
             $content['content'] = $com->getContent();
             $commentsContent[] = $content;
        }

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
            'tags' => $tagsArr,
            'comments' => $commentsContent,
        ]);
    }


}