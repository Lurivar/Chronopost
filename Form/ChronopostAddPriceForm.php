<?php

namespace Chronopost\Form;

use Chronopost\Chronopost;
use Chronopost\Model\ChronopostDeliveryModeQuery;
use Symfony\Component\Validator\Constraints;

use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\AreaQuery;

class ChronopostAddPriceForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add("area", "integer", array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        "methods" => array(
                            array($this,
                                "verifyAreaExist")
                        )
                    ))
                )
            ))
            ->add("delivery_mode", "integer", array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        "methods" => array(
                            array($this,
                                "verifyDeliveryModeExist")
                        )
                    ))
                )
            ))
            ->add("weight", "number", array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        "methods" => array(
                            array($this,
                                "verifyValidWeight")
                        )
                    ))
                )
            ))
            ->add("price", "number", array(
                "constraints" => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        "methods" => array(
                            array($this,
                                "verifyValidPrice")
                        )
                    ))
                )
            ))
            ->add("franco", "number", array())
        ;
    }

    public function verifyAreaExist($value, ExecutionContextInterface $context)
    {
        $area = AreaQuery::create()->findPk($value);
        if (null === $area) {
            $context->addViolation(Translator::getInstance()->trans("This area doesn't exists.", [], Chronopost::DOMAIN_NAME));
        }
    }

    public function verifyDeliveryModeExist($value, ExecutionContextInterface $context)
    {
        $mode = ChronopostDeliveryModeQuery::create()->findPk($value);
        if (null === $mode) {
            $context->addViolation(Translator::getInstance()->trans("This delivery mode doesn't exists.", [], Chronopost::DOMAIN_NAME));
        }
    }

    public function verifyValidWeight($value, ExecutionContextInterface $context)
    {
        if (!preg_match("#^\d+\.?\d*$#", $value)) {
            $context->addViolation(Translator::getInstance()->trans("The weight value is not valid.", [], Chronopost::DOMAIN_NAME));
        }

        if ($value < 0) {
            $context->addViolation(Translator::getInstance()->trans("The weight value must be superior to 0.", [], Chronopost::DOMAIN_NAME));
        }
    }

    public function verifyValidPrice($value, ExecutionContextInterface $context)
    {
        if (!preg_match("#^\d+\.?\d*$#", $value)) {
            $context->addViolation(Translator::getInstance()->trans("The price value is not valid.", [], Chronopost::DOMAIN_NAME));
        }
    }

    public function getName()
    {
        return "chronopost_price_create";
    }
}