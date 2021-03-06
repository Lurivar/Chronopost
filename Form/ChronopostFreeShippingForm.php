<?php

namespace Chronopost\Form;


use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class ChronopostFreeShippingForm extends BaseForm
{
    /**
     * @return null|void
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                "delivery_mode",
                "integer"
            )
            ->add(
                "freeshipping",
                "checkbox",
                [
                    'label'=>Translator::getInstance()->trans("Activate free shipping: ")
                ]
            )
        ;
    }

    /**
     * The name of you form. This name must be unique
     *
     * @return string
     */
    public function getName()
    {
        return "chronopost_freeshipping";
    }

}