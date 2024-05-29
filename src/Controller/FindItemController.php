<?php

namespace App\Controller;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchPhrasePrefix;
use Elastica\Query\Nested;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FindItemController extends AbstractController
{


    public function __construct(
//        private readonly TransformedFinder $finder,
    )
    {
    }


    #[Route('/find/item', name: 'find_item', methods: ['GET', 'POST'])]
    public function findItem(Request $request): Response
    {
        $query = new Query();
        $boolQuery = new BoolQuery();
        $search = $request->get('q');

        $collectionQuery = new Nested();
        $collectionQuery->setPath('item_collection');

        $collectionQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_collection.name',
                $search
            )
        );

        $collectionQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_collection.description',
                $search
            )
        );


        $categoryQuery = new Nested();
        $categoryQuery->setPath('item_collection.collection_category');
        $categoryQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_collection.collection_category.name',
                $search
            )
        );


        $customAttributeQuery = new Nested();

        $customAttributeQuery->setPath('item_collection.custom_item_attributes');
        $customAttributeQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_collection.custom_item_attributes.name',
                $search
            )
        );

        $customAttributeValueQuery = new Nested();
        $customAttributeValueQuery->setPath('item_attribute_value');
        $customAttributeValueQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_attribute_value.name',
                $search
            )
        );

        $nameQuery = new MatchPhrasePrefix('name', $search);




        $boolQuery->addShould($collectionQuery);
        $boolQuery->addShould($nameQuery);
        $boolQuery->addShould($categoryQuery);
        $boolQuery->addShould($customAttributeQuery);
        $boolQuery->addShould($customAttributeValueQuery);

        $query->setQuery($boolQuery);


     //   $ans = $this->finder->find($query);



        $items = [];
//
//        foreach ($ans as $an) {
//            $items[] = $an->getId();
//        }


        return $this->render('item/search.html.twig', [
            'items' => $items,
        ]);
    }

}