<?php

namespace App\Form;

use App\Entity\CollectionCategory;
use App\Entity\ItemsCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as BaseCollectionType;

class CollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('collectionCategory', EntityType::class, [
                'class' => CollectionCategory::class,
                'choice_label' => 'name',
            ])
            ->add('customItemAttributes', BaseCollectionType::class, [
                'entry_type' => CustomAttributeType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                ]
            )
        ;
//        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
//            $collection = $event->getData();
//            $collection->setUser();
//
//        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemsCollection::class,
        ]);
    }
}
