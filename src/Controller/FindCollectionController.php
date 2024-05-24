<?php

namespace App\Controller;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchPhrasePrefix;
use Elastica\Query\MatchQuery;
use Elastica\Query\MultiMatch;
use Elastica\Query\Nested;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Component\Routing\Attribute\Route;

class FindCollectionController
{

    public function __construct(
        private readonly TransformedFinder $finder,
    )
    {
    }


    #[Route('/hello', name: 'hello', methods: ['GET', 'POST'])]
    public function findsomething()
    {
        $query = new Query();
        $boolQuery = new BoolQuery();


        $collectionQuery = new Nested();
        $collectionQuery->setPath('item_collection');
        $collectionQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_collection.name',
                'asfasfadsbrsertabwbtsd'
            )
        );
        $collectionQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_collection.description',
                'asfasfadsbrsertabwbtsd'
            )
        );


        $categoryQuery = new Nested();
        $categoryQuery->setPath('item_collection.collection_category');
        $categoryQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_collection.collection_category.name',
                'asfasfadsbrsertabwbtsd'
            )
        );


        $customAttributeQuery = new Nested();
        $customAttributeQuery->setPath('item_collection.custom_item_attributes');
        $customAttributeQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_collection.custom_item_attributes.name',
                'asfasfadsbrsertabwbtsd'
            )
        );

        $customAttributeValueQuery = new Nested();
        $customAttributeValueQuery->setPath('item_attribute_value');
        $customAttributeValueQuery->setQuery(
            new Query\MatchPhrasePrefix(
                'item_attribute_value.name',
                'IDK'
            )
        );

        $nameQuery = new MatchPhrasePrefix('name', ';pl');


        $boolQuery->addShould($collectionQuery);
        $boolQuery->addShould($nameQuery);
        $boolQuery->addShould($categoryQuery);
        $boolQuery->addShould($customAttributeQuery);
        $boolQuery->addShould($customAttributeValueQuery);

        $query->setQuery($boolQuery);


        $ans = $this->finder->find($query);

        dd($ans);

    }

}