<?php

namespace Application\Form;

use Zend\Filter;
use Zend\Validator;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class ContractForm
 *
 * @package Application\Form
 */
class ContractForm extends Form
{
    /**
     * ContractForm constructor.
     */
    public function __construct()
    {
        parent::__construct('contractForm', []);

        $this->addElements()->addInputFilter();

        $this->setAttribute('class', 'needs-validation');
    }

    /**
     * @return \Application\Form\ContractForm
     */
    protected function addElements(): self
    {
        $this->add([
            'type'       => Element\Hidden::class,
            'name'       => 'id',
            'attributes' => [
                'type' => 'hidden',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'etpContractNumber',
            'options'    => [
                'label' => '№ договора с ЭТП ГПБ',
            ],
            'attributes' => [
                'type'        => 'text',
                'required'    => true,
                'class'       => 'form-control etpContractNumber ',
                'pattern'     => '.{3,50}',
                'placeholder' => '',
            ],
        ]);

        $this->add([
            'type'       => Element\Date::class,
            'name'       => 'etpContractDate',
            'options'    => [
                'label' => 'Дата заключения',
            ],
            'attributes' => [
                'type'        => 'text',
                'required'    => true,
                'class'       => 'form-control etpContractDate',
                'max'         => (new \DateTime())->format('Y-m-d'),
                'placeholder' => '',
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
     * @return \Application\Form\ContractForm
     */
    protected function addInputFilter(): self
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'       => 'etpContractNumber',
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
                        'min' => 3,
                        'max' => 50,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'etpContractDate',
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

        return $this;
    }
}
