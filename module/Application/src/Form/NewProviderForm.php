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
 * Class NewProviderForm
 *
 * @package Application\Form
 */
class NewProviderForm extends Form
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
     * NewProviderForm constructor.
     *
     * @param \ZfbUser\Options\NewUserFormOptionsInterface $options
     * @param \ZfbUser\Options\ReCaptchaOptionsInterface   $recaptchaOptions
     */
    public function __construct(NewUserFormOptionsInterface $options, ReCaptchaOptionsInterface $recaptchaOptions)
    {
        $this->formOptions = $options;
        $this->recaptchaOptions = $recaptchaOptions;

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
     * @return \Application\Form\NewProviderForm
     */
    protected function addElements(): self
    {
        $this->add([
            'type'       => Element\Hidden::class,
            'name'       => 'type',
            'attributes' => [
                'value'    => 'provider',
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
                'class'    => 'identity',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'phone',
            'options'    => [
                'label' => 'Phone',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => false,
                'class'    => 'phone',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'surname',
            'options'    => [
                'label' => 'Surname',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'surname',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'name',
            'options'    => [
                'label' => 'Name',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'name',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'patronymic',
            'options'    => [
                'label' => 'Patronymic',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => false,
                'class'    => 'patronymic',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'fullName',
            'options'    => [
                'label' => 'fullName',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'fullName',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'inn',
            'options'    => [
                'label' => 'ИНН',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'inn',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'kpp',
            'options'    => [
                'label' => 'КПП',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'kpp',
            ],
        ]);

        $this->add([
            'type'       => Element\Text::class,
            'name'       => 'etpContractNumber',
            'options'    => [
                'label' => '№ договора с ЭТП ГПБ',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'form-control etpContractNumber ',
                'placeholder'    => '№ договора с ЭТП ГПБ',
            ],
        ]);

        $this->add([
            'type'       => Element\Date::class,
            'name'       => 'etpContractDate',
            'options'    => [
                'label' => 'Дата договора с ЭТП ГПБ',
            ],
            'attributes' => [
                'type'     => 'date',
                'required' => true,
                'class'    => 'form-control etpContractDate',
                'max'    => (new \DateTime())->format('Y-m-d'),
                'placeholder'    => 'Дата договора',
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
            ->setLabel($this->getFormOptions()->getSubmitButtonText())
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
     * @return \Application\Form\NewProviderForm
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
