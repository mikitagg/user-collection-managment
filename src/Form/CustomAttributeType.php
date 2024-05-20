<?php

namespace App\Form;

use App\Entity\CustomItemAttribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Enum\CustomAttributeType as CustomAttributeTypeEnum;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CustomAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type', EnumType::class, [
                'class' => CustomAttributeTypeEnum::class,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomItemAttribute::class,
        ]);
    }

}