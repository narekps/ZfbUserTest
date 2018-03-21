<?php

namespace Application\Form;

use Zend\Filter;
use Zend\Validator;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Application\Entity\Provider as ProviderEntity;
use Application\Repository\ContractRepository;

/**
 * Class InvoiceForm
 *
 * @package Application\Form
 */
class InvoiceForm extends Form
{
    /**
     * @var ContractRepository
     */
    private $contractRepository;

    /**
     * InvoiceForm constructor.
     *
     * @param \Application\Repository\ContractRepository $contractRepository
     */
    public function __construct(ContractRepository $contractRepository)
    {
        parent::__construct('invoiceForm', []);

        $this->contractRepository = $contractRepository;
        $this->addElements()->addInputFilter();
        $this->setAttribute('class', 'needs-validation');
    }

    /**
     * @param \Application\Entity\Provider $provider
     *
     * @return \Application\Form\InvoiceForm
     */
    public function prepareForProvider(ProviderEntity $provider): self
    {
        $contractOptions = $this->contractRepository->getForSelect($provider);

        /** \Zend\Form\Element\Select */
        $contractIdField = $this->get('contract_id');
        $contractIdField->setOptions([
            'value_options' => $contractOptions,
        ]);


        $contractIdFilter = $this->getInputFilter()->get('contract_id');
        $contractIdFilter->getValidatorChain()->attachByName(Validator\Callback::class, [
            'callback' => function ($value) use ($contractOptions, $contractIdFilter) {
                if (empty($value)) {
                    return !$contractIdFilter->isRequired();
                }


                if (!isset($contractOptions[$value])) {
                    return false;
                }

                return true;
            },
        ]);

        return $this;
    }

    /**
     * @return \Application\Form\InvoiceForm
     */
    protected function addElements(): self
    {
        $this->add([
            'type'       => Element\Select::class,
            'name'       => 'contract_id',
            'options'    => [
                'label'         => 'Договор',
                'value_options' => [],
            ],
            'attributes' => [
                'type'     => 'select',
                'required' => true,
                'class'    => 'form-control contract',
            ],
        ]);

        $this->add([
            'type'       => Element\Date::class,
            'name'       => 'invoiceDate',
            'options'    => [
                'label' => 'Дата выставления счета',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'min'      => (new \DateTime())->format('Y-m-d'),
                'class'    => 'form-control invoiceDate',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'name',
            'options'    => [
                'label' => 'Наименование услуги',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '.{1,1024}',
                'class'    => 'form-control name',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'sum',
            'options'    => [
                'label' => 'Сумма',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '[0-9]{1,12}',
                'class'    => 'form-control sum',
            ],
        ]);

        $this->add([
            'type'       => Element\Select::class,
            'name'       => 'currency',
            'options'    => [
                'label'         => 'Валюта',
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

        $this->add([
            'type'       => Element\Select::class,
            'name'       => 'nds',
            'options'    => [
                'label'         => 'Ставка НДС',
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
            'type'       => Element\Text::class,
            'name'       => 'clientFullName',
            'options'    => [
                'label' => 'Полное юридическое наименование плательщика',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '.{2,1024}',
                'class'    => 'form-control clientFullName',
            ],
        ]);

        $this->add([
            'type'       => Element\Number::class,
            'name'       => 'clientInn',
            'options'    => [
                'label' => 'ИНН плательщика',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '[0-9]{10}|[0-9]{12}',
                'class'    => 'form-control clientInn',
            ],
        ]);

        $this->add([
            'type'       => Element\Number::class,
            'name'       => 'clientKpp',
            'options'    => [
                'label' => 'КПП плательщика',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '[0-9]{9}',
                'class'    => 'form-control clientKpp',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'clientAddress',
            'options'    => [
                'label' => 'Юридический адрес плательщика',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '.{1,300}',
                'class'    => 'form-control clientAddress',
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
     * @return \Application\Form\InvoiceForm
     */
    protected function addInputFilter(): self
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'       => 'contract_id',
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
            ],
        ]);

        $inputFilter->add([
            'name'       => 'invoiceDate',
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
                        'max' => 1000,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'sum',
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
                    'name'    => Filter\Callback::class,
                    'options' => [
                        'callback' => function ($value) {
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
            'name'       => 'clientFullName',
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
                        'min' => 2,
                        'max' => 1024,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'clientInn',
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
                        'min' => 10,
                        'max' => 12,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'clientKpp',
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
                        'min' => 9,
                        'max' => 9,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'clientAddress',
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
                        'max' => 300,
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
