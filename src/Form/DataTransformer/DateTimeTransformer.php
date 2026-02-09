<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimeTransformer implements DataTransformerInterface
{
    private string $format;

    public function __construct(string $format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    public function transform(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTime) {
            return $value->format($this->format);
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($this->format);
        }

        return '';
    }

    public function reverseTransform(mixed $value): ?\DateTime
    {
        if (empty($value) || $value === '') {
            return null;
        }

        $dateTime = \DateTime::createFromFormat($this->format, (string)$value);
        
        if (!$dateTime) {
            throw new TransformationFailedException('Invalid date format. Expected: ' . $this->format);
        }

        return $dateTime;
    }
}
