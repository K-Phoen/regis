<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditRepositoryConfigurationType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', null, ['disabled' => true])
            ->add('sharedSecret')
            ->add('save', SubmitType::class)

            ->setDataMapper($this)
        ;
    }

    public function mapDataToForms($data, $forms)
    {
        $forms = iterator_to_array($forms);
        $forms['identifier']->setData($data->getIdentifier());
        $forms['sharedSecret']->setData($data->getSharedSecret());
    }

    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        $data = [
            'sharedSecret' => $forms['sharedSecret']->getData(),
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'intent' => 'edit_repository',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'edit_repository_type';
    }
}
