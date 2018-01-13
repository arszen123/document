<?php
/**
 * Created by PhpStorm.
 * User: after8
 * Date: 1/12/18
 * Time: 9:17 PM
 */

namespace Document\Model;


use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\InArray;

class EditPermission implements InputFilterAwareInterface
{

    private $inputFilter;

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @return InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    /**
     * Retrieve input filter
     *
     * @return InputFilterInterface
     */
    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'upload',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => InArray::class,
                    'options' => [
                        'haystack' => [true, false],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'download',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
            [
                'name' => InArray::class,
                'options' => [
                    'haystack' => [true, false],
                ],
            ],
        ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }


}