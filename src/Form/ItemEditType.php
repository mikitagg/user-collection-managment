<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemEditType extends AbstractType
{

    public function __construct(
        private readonly TagTransformer $tagTransformer,
    )
    {

    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('itemTags', TextType::class, [
                'required' => false,
                'autocomplete' => true,
                'tom_select_options' => [
                    'create' => true,
                    'createOnBlur' => true,
                    'delimiter' => ',',
                ],
                'autocomplete_url' => '/autocomplete/tags',
            ])
            ->add('itemAttributeValue', CollectionType::class,
                [
                    'entry_type' => ItemAttributeValueType::class,
                    'entry_options' => ['label' => false],
                ]);
        $builder->get('itemTags')->addModelTransformer($this->tagTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }

}