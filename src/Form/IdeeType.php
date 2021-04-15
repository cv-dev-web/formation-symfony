<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class IdeeType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un champ
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfiguration($label, $placeholder,$options =[])
    {
        return array_merge([
            'label'=> $label,
            'attr'=> [
                'placeholder'=> $placeholder
            ]
        ],$options);
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class, $this->getConfiguration("Titre","Tapez un super titre pour votre invention"))
            ->add('slug',TextType::class, $this->getConfiguration("Adresse Web", "Adresse Web (Automatique)",
            [
                'required'=> false
            ]))
            ->add('coverImage', UrlType::class, $this->getConfiguration("Url de l'image principale","Donnez l'adresse d'un croquis qui fait envie"))
            ->add('introduction',TextType::class, $this->getConfiguration("Introduction", "Donnez une description global du projet"))
            ->add('content',TextareaType::class, $this->getConfiguration("Description detaillée","tapez une description qui enflamme les idées!!"))
            ->add('idees',IntegerType::class, $this->getConfiguration("Nombre d'idées","Le nombre d'idées que vous avez"))
            ->add('price',MoneyType::class, $this->getConfiguration("Prix par projet","Indiquez le prix désiré du projet"))
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type'=> ImageType::class,
                    'allow_add' => true,
                    'allow_delete'=>true
                ])   
            //=> rajoute un element au formulaire -> ici button submit
            /* ->add('save',SubmitType::class,
            [
                'label'=>'Créer la nouvelle invention',
                'attr'=> [
                    'class'=>'btn btn-primary'
                ]
            ]) */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
