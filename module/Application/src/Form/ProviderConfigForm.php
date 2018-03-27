<?php

namespace Application\Form;

use Zend\Filter;
use Zend\Validator;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class ProviderConfigForm
 *
 * @package Application\Form
 */
class ProviderConfigForm extends Form
{
    /**
     * ProviderConfigForm constructor.
     */
    public function __construct()
    {
        parent::__construct('providerConfigForm', []);

        $this->addElements()->addInputFilter();
        $this->setAttribute('class', 'needs-validation');
    }

    /**
     * @return \Application\Form\ProviderConfigForm
     */
    protected function addElements(): self
    {
        $this->add([
            'type'       => Element\Hidden::class,
            'name'       => 'provider_id',
            'attributes' => [
                'type'  => 'hidden',
                'class' => 'form-control provider_id',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'identifier',
            'options'    => [
                'label' => 'Идентификатор',
            ],
            'attributes' => [
                'type'     => 'text',
                'readonly' => true,
                'class'    => 'form-control identifier',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'privateKey',
            'options'    => [
                'label' => 'Приватный ключ',
            ],
            'attributes' => [
                'type'     => 'text',
                'readonly' => true,
                'class'    => 'form-control privateKey',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'paymentsUrl',
            'options'    => [
                'label' => 'URL для отсылки платежей',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => false,
                'pattern'  => '.{0,100}',
                'class'    => 'form-control paymentsUrl',
            ],
        ]);

        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel('Сохранить')
            ->setAttributes([
                'type'  => Element\Submit::class,
                'class' => 'submit disabled',
            ]);

        $this->add($submitElement, [
            'priority' => -100,
        ]);

        $csrf = new Element\Csrf('csrf');
        $csrf->getCsrfValidator()->setTimeout(60 * 5);
        $this->add($csrf);

        return $this;
    }

    /**
     * @return \Application\Form\ProviderConfigForm
     */
    protected function addInputFilter(): self
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'       => 'identifier',
            'required'   => true,
            'filters'    => [
                [
                    'name' => Filter\StripTags::class,
                ],
                [
                    'name' => Filter\StripNewlines::class,
                ],
                [
                    'name' => Filter\StringTrim::class,
                ],
                [
                    'name' => Filter\ToNull::class,
                ],
            ],
            'validators' => [
                [
                    'name' => Validator\Uuid::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'privateKey',
            'required'   => true,
            'filters'    => [
                [
                    'name' => Filter\StripTags::class,
                ],
                [
                    'name' => Filter\StringTrim::class,
                ],
                [
                    'name' => Filter\ToNull::class,
                ],
            ],
            'validators' => [
                [
                    'name' => Validator\NotEmpty::class,
                ],
                [
                    'name'    => Validator\StringLength::class,
                    'options' => [
                        'min' => 100,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'paymentsUrl',
            'required'   => false,
            'filters'    => [
                [
                    'name' => Filter\StripTags::class,
                ],
                [
                    'name' => Filter\StringTrim::class,
                ],
                [
                    'name' => Filter\ToNull::class,
                ],
            ],
            'validators' => [
                [
                    'name' => Validator\Uri::class,
                ],
                [
                    'name'    => Validator\StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
