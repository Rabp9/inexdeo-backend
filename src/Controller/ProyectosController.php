<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Proyectos Controller
 *
 * @property \App\Model\Table\ProyectosTable $Proyectos
 */
class ProyectosController extends AppController
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
        $proyectos = $this->Proyectos->find()
            ->where(['estado_id' => 1])
            ->contain('ProyectoImages');
        
        $this->set(compact('proyectos'));
        $this->set('_serialize', ['proyectos']);
    }
    
    public function view($id) {
        $proyecto = $this->Proyectos->get($id, [
            'contain' => 'ProyectoImages'
        ]);
        
        $this->set(compact('proyecto'));
        $this->set('_serialize', ['proyecto']);
    }
    
    public function getRandom($num = null) {
        $num = $this->request->param('num');
        
        $proyectos = $this->Proyectos->find()
            ->where(['estado_id' => 1])
            ->limit($num)
            ->order('rand()');
        
        $this->set(compact('proyectos'));
        $this->set('_serialize', ['proyectos']);
    }
}
