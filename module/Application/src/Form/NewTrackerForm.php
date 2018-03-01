<?php

namespace Application\Form;

use Zend\Captcha\ReCaptcha;
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
     * NewUserForm constructor.
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
     * @return \Application\Form\NewTrackerForm
     */
    protected function addElements(): self
    {
        $this->add([
            'name'       => 'type',
            'attributes' => [
                'value'    => 'tracker',
                'type'     => 'hidden',
                'required' => true,
            ],
        ]);

        $this->add([
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
            'name'       => 'patronymic',
            'options'    => [
                'label' => 'Patronymic',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'patronymic',
            ],
        ]);

        $this->add([
            'name'       => 'trackingProviders',
            'options'    => [
                'label' => 'trackingProviders',
            ],
            'attributes' => [
                'type'     => 'text',
                'required' => true,
                'class'    => 'trackingProviders',
            ],
        ]);

        if ($this->formOptions->isEnabledRecaptcha()) {
            $reCaptcha = new ReCaptcha($this->recaptchaOptions->toArray());
            $this->add([
                'name'    => 'captcha',
                'type'    => 'captcha',
                'options' => [
                    'captcha' => $reCaptcha,
                ],
            ]);
        }

        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel($this->getFormOptions()->getSubmitButtonText())
            ->setAttributes([
                'type'  => 'submit',
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
            'validators' => [
                [
                    'name' => 'EmailAddress',
                ],
            ],
        ]);

        return $this;
    }
}
