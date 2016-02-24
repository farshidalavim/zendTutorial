<?php
namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Description of AlbumTable
 *
 * @author farshidalavimoghadam
 */
class AlbumTable {
    protected $tableGateway;

    /**
     * __constructor function using TableGateway
     * 
     * @param TableGateway $tableGateway
     * @return null
     */
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    /**
     * getAlbum to get a specific album with given id from database
     * 
     * @param int $id
     * @return Album $row
     */
    public function getAlbum($id) {
        $rowset = $this->tableGateway->select(array('id' => (int) $id));
        $row = $rowset->current();
        
        if(!$row) {
            throw new \Exception("Could not find row $id");
        }
         
        return $row;
    }

    public function saveAlbum(Album $album) {
        $data = array(
            'artist' => $album -> artist,
            'title'  => $album -> title,
        );

        $id = (int) $album->id;
        
        if($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if($this->getAlbum($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Album id does not exist');
            }
         }
     }

    public function deleteAlbum($id) {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
