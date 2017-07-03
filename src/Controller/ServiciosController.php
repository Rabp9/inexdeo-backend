<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Servicios Controller
 *
 * @property \App\Model\Table\ServiciosTable $Servicios
 */
class ServiciosController extends AppController
{
    public function initialize() {
        parent::initialize();
        $this->Auth->allow(['getRandom', 'index', 'view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $servicios = $this->Servicios->find()
            ->where(['estado_id' => 1])
            ->contain('ServicioImages');
        
        $this->set(compact('servicios'));
        $this->set('_serialize', ['servicios']);
    }
    
    public function view($id) {
        $servicio = $this->Servicios->get($id, [
            'contain' => 'ServicioImages'
        ]);
        
        $this->set(compact('servicio'));
        $this->set('_serialize', ['servicio']);
    }
    
    public function getRandom($num = null) {
        $num = $this->request->param('num');
        
        $servicios = $this->Servicios->find()
            ->where(['estado_id' => 1])
            ->limit($num)
            ->order('rand()');
        
        $this->set(compact('servicios'));
        $this->set('_serialize', ['servicios']);
    }
}
