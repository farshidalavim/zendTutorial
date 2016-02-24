<?php
namespace Album\Model;

/**
 * Model class Album for database gateway
 */
class Album {
    public $id;
    public $artist;
    public $title;

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
    public function exchangeArray(array $data) {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->artist = (!empty($data['artist'])) ? $data['artist'] : null;
        $this->title  = (!empty($data['title'])) ? $data['title'] : null;
    }
}
