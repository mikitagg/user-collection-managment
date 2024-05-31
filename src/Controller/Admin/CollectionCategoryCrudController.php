<?php

namespace App\Controller\Admin;

use App\Entity\CollectionCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


class CollectionCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CollectionCategory::class;
    }
}
