<?php

namespace Application\Form;

use Zend\Filter;
use Zend\Validator;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class TariffForm
 *
 * @package Application\Form
 */
class TariffForm extends Form
{
    /**
     * TariffForm constructor.
     */
    public function __construct()
    {
        parent::__construct('tariffForm', []);

        $this->addElements()->addInputFilter();

        $this->setAttribute('class', 'needs-validation');
    }

    /**
     * @return \Application\Form\TariffForm
     */
    protected function addElements(): self
    {
        $this->add([
            'type'       => Element\Hidden::class,
            'name'       => 'id',
            'attributes' => [
                'type'     => 'hidden',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'name',
            'options'    => [
                'label' => 'Название',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '.{1,500}',
                'class'    => 'form-control name',
            ],
        ]);

        $this->add([
            'type'       => Element\Textarea::class,
            'name'       => 'description',
            'options'    => [
                'label' => 'Описание',
            ],
            'attributes' => [
                'type'     => 'textarea',
                'rows'     => 3,
                'required' => true,
                'pattern'  => '.{1,2000}',
                'class'    => 'form-control description',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'cost',
            'options'    => [
                'label' => 'Стоимость',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern' => '[0-9]{1,12}',
                'class'    => 'form-control cost',
            ],
        ]);

        $this->add([
            'type'       => Element\Select::class,
            'name'       => 'nds',
            'options'    => [
                'label' => 'Ставка НДС',
                'value_options' => [
                    '-1' => 'Без НДС',
                    '0'  => '0%',
                    '10' => '10%',
                    '18' => '18%',
                ],
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'form-control nds',
            ],
        ]);

        $this->add([
            'type'       => Element\Date::class,
            'name'       => 'saleEndDate',
            'options'    => [
                'label' => 'Дата окончания продаж',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'min'      => (new \DateTime())->format('Y-m-d'),
                'class'    => 'form-control saleEndDate',
            ],
        ]);

        $this->add([
            'type'       => Element\Select::class,
            'name'       => 'currency',
            'options'    => [
                'label' => 'Валюта',
                'value_options' => [
                    'RUB' => 'RUB',
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                ],
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'form-control currency',
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
     * @return \Application\Form\TariffForm
     */
    protected function addInputFilter(): self
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'       => 'name',
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
                    'name' => Validator\NotEmpty::class,
                ],
                [
                    'name'    => Validator\StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 500,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'description',
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
                        'min' => 1,
                        'max' => 2000,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'cost',
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
                    'name' => Filter\ToInt::class,
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
                    'name'    => Validator\GreaterThan::class,
                    'options' => [
                        'min'       => 1.00,
                        'inclusive' => false,
                    ],
                ]
            ],
        ]);

        $inputFilter->add([
            'name'       => 'nds',
            'required'   => false,
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
                    'name' => Filter\ToInt::class,
                ],
                [
                    'name' => Filter\Callback::class,
                    'options' => [
                        'callback' => function($value) {
                            if ($value == -1) {
                                return null;
                            }

                            return $value;
                        }
                    ]
                ],
            ],
            'validators' => [
                [
                    'name'    => Validator\InArray::class,
                    'options' => [
                        'haystack' => [0, 10, 18, null],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'saleEndDate',
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
                    'name' => Validator\NotEmpty::class,
                ],
                [
                    'name' => Validator\Date::class,
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'currency',
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
                    'name' => Validator\NotEmpty::class,
                ],
                [
                    'name'    => Validator\InArray::class,
                    'options' => [
                        'haystack' => ['RUB', 'USD', 'EUR'],
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
