<?php

namespace App\Controller;

use App\Entity\ItemTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/autocomplete/tags', name: 'autocomplete_tags',  methods: [Request::METHOD_GET])]
    public function getTags(Request $request): JsonResponse
    {
        $query = $request->query->get('query');
        $tags = $this->entityManager->getRepository(ItemTag::class)->findByQuery($query);

        $tagList = [];

        foreach ($tags as $tag) {
            $tagList[] = [
                'value' => $tag->getId(),
                'text' => $tag->getName(),
            ];
        }
        return new JsonResponse(['results'=>$tagList]);
    }

}