<?php

namespace Application\Form;

use Zend\Filter;
use Zend\I18n\Validator\PhoneNumber;
use Zend\Validator;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class EditTrackerForm
 *
 * @package Application\Form
 */
class EditTrackerForm extends Form
{
    /**
     * @var array
     */
    protected $providerOptions;

    /**
     * EditTrackerForm constructor.
     *
     * @param string|null $name
     * @param array       $options
     * @param array       $providerOptions
     */
    public function __construct(string $name = null, array $options = [], array $providerOptions = [])
    {
        $this->providerOptions = $providerOptions;

        parent::__construct('contragentEditForm', []);

        $this->addElements()->addInputFilter();
    }

    /**
     * @return \Application\Form\EditTrackerForm
     */
    protected function addElements(): self
    {
        $this->add([
            'type'       => Element\Hidden::class,
            'name'       => 'id',
            'attributes' => [
                'type'     => 'hidden',
                'required' => true,
                'class'    => 'form-control id',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'phone',
            'options'    => [
                'label' => 'Телефон',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'form-control phone',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'fullName',
            'options'    => [
                'label' => 'Наименование сервис-провайдера',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '.{2,1024}',
                'class'    => 'form-control fullName',
            ],
        ]);

        $this->add([
            'type'       => Element\Number::class,
            'name'       => 'inn',
            'options'    => [
                'label' => 'ИНН',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '[0-9]{10}|[0-9]{12}',
                'class'    => 'form-control inn',
            ],
        ]);

        $this->add([
            'type'       => Element\Number::class,
            'name'       => 'kpp',
            'options'    => [
                'label' => 'КПП',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '[0-9]{9}',
                'class'    => 'form-control kpp',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'address',
            'options'    => [
                'label' => 'Юридический адрес',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '.{1,300}',
                'class'    => 'form-control address',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'contactPerson',
            'options'    => [
                'label' => 'Контактное лицо',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '.{1,100}',
                'class'    => 'form-control contactPerson',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'email',
            'options'    => [
                'label' => 'E-mail организации',
            ],
            'attributes' => [
                'type'     => 'email',
                'required' => true,
                'pattern'  => '.{2,50}',
                'class'    => 'form-control email',
            ],
        ]);

        $this->add([
            'type'       => Element\Select::class,
            'name'       => 'trackingProviders',
            'options'    => [
                'label'         => 'Подконтрольные сервис-провайдеры',
                'value_options' => $this->providerOptions,
            ],
            'attributes' => [
                'type'     => 'select',
                'multiple' => 'multiple',
                'required' => false,
                'class'    => 'form-control trackingProviders',
            ],
        ]);

        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel('Сохранить')
            ->setAttributes([
                'type'  => Element\Submit::class,
                'class' => 'submit',
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
     * @return \Application\Form\EditTrackerForm
     */
    protected function addInputFilter(): self
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'       => 'phone',
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
                [
                    'name'    => Filter\Callback::class,
                    'options' => [
                        'callback' => function ($value) {
                            $maskSymbols = ['(', ')', '-', ' '];
                            foreach ($maskSymbols as $maskSymbol) {
                                $value = str_replace($maskSymbol, '', $value);
                            }

                            return $value;
                        }
                    ],
                ],
            ],
            'validators' => [
                [
                    'name'    => PhoneNumber::class,
                    'options' => [
                        'country' => 'RU',
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'fullName',
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
            'name'       => 'inn',
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
            'name'       => 'kpp',
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
            'name'       => 'address',
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

        $inputFilter->add([
            'name'       => 'contactPerson',
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
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'email',
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
                    'name' => Validator\EmailAddress::class,
                ],
                [
                    'name'    => Validator\StringLength::class,
                    'options' => [
                        'min' => 2,
                        'max' => 50,
                    ],
                ],
            ],
        ]);

        $providerOptions = $this->providerOptions;
        $inputFilter->add([
            'name'       => 'trackingProviders',
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
                    'name'    => Validator\Callback::class,
                    'options' => [
                        'callback' => function ($values) use ($providerOptions) {
                            $values = array_filter($values);
                            if (empty($values)) {
                                return true;
                            }

                            foreach ($values as $value) {
                                if (!isset($providerOptions[$value])) {
                                    return false;
                                }
                            }

                            return true;
                        },
                    ],
                ],
            ],
        ]);

        return $this;
    }
}
