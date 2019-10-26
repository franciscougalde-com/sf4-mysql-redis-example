<?php

namespace App\Helper;

use Symfony\Component\Form\FormInterface;

class ErrorHelper
{
    /**
     * @param FormInterface $form
     * @return array
     */
    public static function generateErrorOutputFromForm(FormInterface $form): array
    {
        $errorOutput = [];
        foreach ($form->all() as $element) {
            $field = $element->getName();
            foreach ($element->getErrors() as $formError) {
                $errorOutput['errors'][$field][] = $formError->getMessage();
            }
        }

        return $errorOutput;
    }
}
