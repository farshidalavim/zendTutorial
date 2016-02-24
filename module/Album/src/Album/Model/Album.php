<?php
namespace Album\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Model class Album for database gateway
 */
class Album {
    /**
     * @var $id
     * @var $artist
     * @var $title
     * @var $inputFilter
     */
    public $id;
    public $artist;
    public $title;
    protected $inputFilter;

    /**
     * exchangeArray method
     * 
     * In order to work with Zend\Db’s TableGateway class, we need to implement the exchangeArray() method
     * 
     * This method simply copies the data from the passed in array to our entity’s properties
     * 
     * @param array $data
     * @return null
     */
    public function exchangeArray($data) {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->artist = (!empty($data['artist'])) ? $data['artist'] : null;
        $this->title  = (!empty($data['title'])) ? $data['title'] : null;
    }
    
    /**
     * The form’s bind() method attaches the model to the form. This is used in two ways:
     * When displaying the form, the initial values for each element are extracted from the model.
     * After successful validation in isValid(), the data from the form is put back into the model.
     * These operations are done using a hydrator object.
     */
    public function getArrayCopy() {
        return get_object_vars($this);
    }
    
    /**
     * @param InputFilterInterface $inputFilter
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }
    
    /**
     * Validator function using Zend\InputFilter
     * 
     * @return $inputFilter
     */
    public function getInputFilter() {
        if(!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'artist',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'title',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
         }

        return $this->inputFilter;
    }
}
