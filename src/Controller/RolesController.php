<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Roles Controller
 *
 * @property \App\Model\Table\RolesTable $Roles
 */
class RolesController extends AppController
{
    
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $this->viewBuilder()->layout(false);
        
        $roles = $this->Roles->find()
            ->where(['estado_id' => 1]);

        $this->set(compact('roles'));
        $this->set('_serialize', ['roles']);
    }

    /**
     * Get Admin method
     *
     * @return \Cake\Network\Response|null
     */
    public function getAdmin() {
        $this->viewBuilder()->layout(false);
        
        $roles = $this->Roles->find()
            ->contain(['ControllerRoles.Controllers']);

        $this->set(compact('roles'));
        $this->set('_serialize', ['roles']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $this->viewBuilder()->layout(false);
        $rol = $this->Roles->newEntity();
        $rol->estado_id = 1;
        
        if ($this->request->is('post')) {
            $rol = $this->Roles->patchEntity($rol, $this->request->getData());
            if ($this->Roles->save($rol)) {
                $message =  [
                    'text' => __('El Rol fue guardado correctamente'),
                    'type' => 'success',
                ];
            } else {
                $message =  [
                    'text' => __('El Rol no fue guardado correctamente'),
                    'type' => 'error',
                ];
            }
        }
        $this->set(compact('rol', 'message'));
        $this->set('_serialize', ['rol', 'message']);
    }
}
