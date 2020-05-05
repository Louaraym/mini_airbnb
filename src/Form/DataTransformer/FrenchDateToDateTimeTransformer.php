<?php


namespace App\Form\DataTransformer;


use DateTime;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchDateToDateTimeTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     * @return mixed|string french date format
     */
    public function transform($value)
    {
        if ($value === null){
            return '';
        }

        return $value->format('d/m/Y');
    }

    /**
     * @param mixed $value
     * @return DateTime|false|mixed
     */
    public function reverseTransform($value)
    {
        if ($value === null){
           throw new TransformationFailedException(
               "Vous devez fournir une date"
           );
        }

        $date = DateTime::createFromFormat('d/m/Y', $value);

        if ($date === false){
            throw new TransformationFailedException(
                "Vous devez fournir une date au format dd/mm/yyyy"
            );
        }

        return $date;
    }
}