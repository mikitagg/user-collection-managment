<?php

namespace App\Form;

use App\Entity\ItemTag;
use App\Repository\ItemTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

readonly class TagTransformer implements DataTransformerInterface
{

    public function __construct(
        private ItemTagRepository $itemTagRepository,
    )
    {
    }

    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        $tags = [];

        foreach ($value as $tag) {
            $tags[] = $tag->getName();
        }

        return implode(', ', $tags);
    }

    public function reverseTransform(mixed $value = null): ArrayCollection
    {
        if (!$value) {
            return new ArrayCollection();
        }

        $items = explode(",", $value);
        $items = array_map('trim', $items);
        $items = array_unique($items);

        $value = new ArrayCollection();

        foreach ($items as $item) {
            $tag = $this->itemTagRepository->findOneBy(['name' => $item]);
            if (!$tag) {
                $tag = (new ItemTag())->setName($item);
            }

            $value->add($tag);
        }

        return $value;
    }

}