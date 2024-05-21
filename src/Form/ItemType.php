<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\ItemAttributeValue;
use App\Entity\ItemsCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as BaseCollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
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
             ]);
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $itemCollectionRepository = $this->entityManager->getRepository(ItemsCollection::class);
                $customAttributes = $itemCollectionRepository->findWithCustomAttributes($data->getItemCollection()->getId());

                if($customAttributes) {
                    foreach ($customAttributes->getCustomItemAttributes() as $customAttribute) {
                        $customAttributeType = $customAttribute->getType()->value;
                        switch ($customAttributeType) {
                            case 'INT':
                                $form->add($customAttribute->getName(), IntegerType::class, [
                                    'label' => $customAttribute->getName(),
                                    'mapped' => false,
                                ]);
                                break;
                            case 'DATE':
                                $form->add($customAttribute->getName(), DateType::class, [
                                    'label' => $customAttribute->getName(),
                                    'mapped' => false,
                                ]);
                                break;
                            case 'FLOAT':
                                $form->add($customAttribute->getName(), NumberType::class, [
                                    'label' => $customAttribute->getName(),
                                    'mapped' => false,
                                ]);
                                break;
                            case 'STRING':
                                $form->add($customAttribute->getName(), TextType::class, [
                                    'label' => $customAttribute->getName(),
                                    'mapped' => false,
                                ]);
                                break;
                            case 'BOOL':
                                $form->add($customAttribute->getName(), ChoiceType::class, [
                                    'label' => $customAttribute->getName(),
                                    'mapped' => false,
                                    'choices' => [
                                        'True' => true,
                                        'False' => false,
                                    ]
                                ]);
                                break;
                        }
                    }
                }
            }
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $itemCollectionRepository = $this->entityManager->getRepository(ItemsCollection::class);
                $customAttributes = $itemCollectionRepository->findWithCustomAttributes($data->getItemCollection()->getId());
                $i = 0;
                foreach ($form as $field) {
                    if ($field->getConfig()->getOption('mapped') === false) {
                        $attributeValue = new ItemAttributeValue();
                        $attributeValue->setItem($data->getId());
                        $attributeValue->setName($field->getViewData());
                        $attributeValue->setCustomItemAttribute($customAttributes->getCustomItemAttributes()[$i]);
                        $i++;
                        $data->addItemAttributeValue($attributeValue);
                    }
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
            'itemTags' => null,
        ]);
    }

}