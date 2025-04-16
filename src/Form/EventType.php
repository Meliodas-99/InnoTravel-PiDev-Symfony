<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Organizer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('location')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Conference' => 'Conference',
                    'Workshop' => 'Workshop',
                    'Meetup' => 'Meetup',
                    'Webinar' => 'Webinar',
                    'Festival' => 'Festival',
                    'Other' => 'Other',
                ],
                'placeholder' => 'Choose a category',
            ])
            ->add('price')
            ->add('idOrganizer', EntityType::class, [
                'class' => Organizer::class,
                'choice_label' => 'name', 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
