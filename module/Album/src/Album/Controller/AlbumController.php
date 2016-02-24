<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;          
use Album\Form\AlbumForm;       

/**
 * Album Controller
 */
class AlbumController extends AbstractActionController {
    /**
     * @var AlbumTable
     */
    protected $albumTable;
    
    /**
     * Now that the ServiceManager can create an AlbumTable instance for us, we can add a method to the controller to retrieve it
     * 
     * @return Object instance of \Album\Model\AlbumTable
     */
    public function getAlbumTable() {
        if(!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        
        return $this->albumTable;
    }
    
    /**
     * @return ViewModel
     */
    public function indexAction() {
        /**
         * In order to list the albums, we need to retrieve them from the model and pass them to the view
         */
        return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll(),
        ));
    }

    public function addAction() {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        
        if(!$id) {
            return $this->redirect()->toRoute('album', array(
                 'action' => 'add'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $album = $this->getAlbumTable()->getAlbum($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('album', array('action' => 'index'));
        }

        /**
         * As a result of using bind() with its hydrator, we do not need to populate the form’s data back into the $album as that’s already been done, so we can just call the mappers’ saveAlbum() to store the changes back to the database.
         */
        $form = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        ); 
    }

    public function deleteAction() {
        
    }
}