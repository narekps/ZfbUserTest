<?php

namespace Application\Form;

use Zend\Captcha\ReCaptcha;
use Zend\Filter;
use Zend\I18n\Validator\PhoneNumber;
use Zend\Validator;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use ZfbUser\Options\ReCaptchaOptionsInterface;
use ZfbUser\Options\NewUserFormOptionsInterface;

/**
 * Class NewTrackerForm
 *
 * @package Application\Form
 */
class NewTrackerForm extends Form
{
    /**
     * @var NewUserFormOptionsInterface
     */
    protected $formOptions;

    /**
     * @var ReCaptchaOptionsInterface
     */
    protected $recaptchaOptions;

    /**
     * @var array
     */
    protected $providerOptions;

    /**
     * NewTrackerForm constructor.
     *
     * @param \ZfbUser\Options\NewUserFormOptionsInterface $options
     * @param \ZfbUser\Options\ReCaptchaOptionsInterface   $recaptchaOptions
     * @param array                                        $providerOptions
     */
    public function __construct(NewUserFormOptionsInterface $options, ReCaptchaOptionsInterface $recaptchaOptions, array $providerOptions = [])
    {
        $this->formOptions = $options;
        $this->recaptchaOptions = $recaptchaOptions;
        $this->providerOptions = $providerOptions;

        parent::__construct($options->getFormName(), []);

        $this->addElements()->addInputFilter();
    }

    /**
     * @return \ZfbUser\Options\NewUserFormOptionsInterface
     */
    public function getFormOptions(): NewUserFormOptionsInterface
    {
        return $this->formOptions;
    }

    /**
     * @return \Application\Form\NewTrackerForm
     */
    protected function addElements(): self
    {
        $this->add([
            'type'       => Element\Hidden::class,
            'name'       => 'type',
            'attributes' => [
                'value'    => 'tracker',
                'type'     => 'hidden',
                'required' => true,
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => $this->getFormOptions()->getIdentityFieldName(),
            'options'    => [
                'label' => $this->getFormOptions()->getIdentityFieldLabel(),
            ],
            'attributes' => [
                'type'     => 'email',
                'required' => true,
                'pattern'  => '.{2,50}',
                'class'    => 'form-control ' . $this->getFormOptions()->getIdentityFieldName(),
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
            'name'       => 'surname',
            'options'    => [
                'label' => 'Фамилия',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '.{2,50}',
                'class'    => 'form-control surname',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'name',
            'options'    => [
                'label' => 'Имя',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'pattern'  => '.{2,30}',
                'class'    => 'form-control name',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'patronymic',
            'options'    => [
                'label' => 'Отчество',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => false,
                'pattern'  => '.{0}|.{2,50}',
                'class'    => 'form-control patronymic',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'fullName',
            'options'    => [
                'label' => 'Наименование организации',
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

        if ($this->formOptions->isEnabledRecaptcha()) {
            $reCaptcha = new ReCaptcha($this->recaptchaOptions->toArray());
            $this->add([
                'type'    => Element\Captcha::class,
                'name'    => 'captcha',
                'options' => [
                    'captcha' => $reCaptcha,
                ],
            ]);
        }

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
        $csrf->getCsrfValidator()->setTimeout($this->getFormOptions()->getCsrfTimeout());
        $this->add($csrf);

        return $this;
    }

    /**
     * @return \Application\Form\NewTrackerForm
     */
    protected function addInputFilter(): self
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'       => $this->getFormOptions()->getIdentityFieldName(),
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
            'name'       => 'surname',
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
                        'max' => 50,
                    ],
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
                        'min' => 2,
                        'max' => 30,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'patronymic',
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
                    'name' => Filter\ToNull::class,
                ],
            ],
            'validators' => [
                [
                    'name'    => Validator\StringLength::class,
                    'options' => [
                        'min' => 2,
                        'max' => 50,
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
