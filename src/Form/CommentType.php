<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Ticket;
use App\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text')
            ->add('date')
            ->add('owner',EntityType::class,['class'=>User::class,'choice_label' => 'username'])
            ->add('ticket',EntityType::class,['class'=>Ticket::class,'choice_label' => 'name'])
            //->add('colonne',EntityType::class, ['class' => Colonne::class, 'choice_label' => 'name' ])
                    // $builder->add('colonnes', CollectionType::class, [
        //     'entry_type' => ColonneType::class,
        //     'entry_options' => ['label' => false],
        // ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
