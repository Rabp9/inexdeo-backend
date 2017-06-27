<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\Event;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public function initialize() {
        parent::initialize();
        $this->Auth->allow(['token']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $users = $this->Users->find()
            ->where(['estado_id' => 1])
            ->contain(['Roles']);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function getAdmin() {
        $users = $this->Users->find()
            ->contain(['Roles']);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }
    
    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $user = $this->Users->newEntity();
        $user->estado_id = 1;
        
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $user = $this->Users->get($user->id, ['contain' => ['Roles']]);
                $message =  [
                    'text' => __('El Usuario fue guardado correctamente'),
                    'type' => 'success',
                ];
            } else {
                $message =  [
                    'text' => __('El Usuario no fue guardado correctamente'),
                    'type' => 'error',
                ];
            }
        }
        $this->set(compact('user', 'message'));
        $this->set('_serialize', ['user', 'message']);
    }

    /**
     * Login method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function login() {
        $hasher = new DefaultPasswordHasher();
        
        $user = $this->Users->findByUsername($this->request->getData()['username'])->first();
        
        if (!empty($user)) {
            if (!$hasher->check($this->request->getData()['password'], $user->password)) {
                $user = null;
                $message =  [
                    'text' => __('El password no coincide'),
                    'type' => 'success',
                ];
            } else {
                $user = $this->Users->get($user->id, ['contain' => ['Roles.ControllerRoles.Controllers']]);
            }
        } else {
            $message =  [
                'text' => __('El nombre de usuario no existe'),
                'type' => 'success',
            ];
        }
        
        $this->set(compact('user', 'message'));
        $this->set('_serialize', ['user', 'message']);
    }
    
    public function token() {
        $user = $this->Auth->identify();
        if (!$user) {
            throw new UnauthorizedException('Invalid username or password');
        }
        $user = $this->Users->get($user['id'], ['contain' => ['Roles.ControllerRoles.Controllers']]);
        $this->set([
            'success' => true,
            'user' => $user,
            'token' => JWT::encode([
                'sub' => $user['id'],
                'exp' =>  time() + 604800
            ],
            Security::salt()),
            '_serialize' => ['success', 'user', 'token']
        ]);
    }
}
