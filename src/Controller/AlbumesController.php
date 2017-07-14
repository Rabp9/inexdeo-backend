<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Albumes Controller
 *
 * @property \App\Model\Table\ProductosTable $Albumes
 */
class AlbumesController extends AppController
{
    public function initialize() {
        parent::initialize();
        $this->Auth->allow(['index']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $albumes = $this->Albumes->find()
            ->where(['estado_id' => 1])
            ->contain('Imagenes');
        
        $this->set(compact('albumes'));
        $this->set('_serialize', ['albumes']);
    }
}
