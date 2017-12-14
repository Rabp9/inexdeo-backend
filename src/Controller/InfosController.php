<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Filesystem\File;

/**
 * Infos Controller
 *
 * @property \App\Model\Table\InfosTable $Infos
 *
 * @method \App\Model\Entity\Info[] paginate($object = null, array $settings = [])
 */
class InfosController extends AppController
{
    public function initialize() {
        parent::initialize();
        $this->Auth->allow(['getDataMany', 'getData', 'getDataByData', 'add', 'download']);
    }
    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $info = $this->Infos->newEntity();
        if ($this->request->is('post')) {
            $info = $this->Infos->patchEntity($info, $this->request->data);
            if ($this->Infos->save($info)) {
                $message =  [
                    'text' => __('La información fue guardada correctamente'),
                    'type' => 'success',
                ];
            } else {
                $message =  [
                    'text' => __('La información no fue guardada correctamente'),
                    'type' => 'error',
                ];
            }
        }
        $this->set(compact('info', 'message'));
        $this->set('_serialize', ['info', 'message']);
    }
    
    /**
     * Save Many method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function saveMany() {
        if ($this->request->is('post')) {
            $infos = $this->request->data;
            foreach ($infos as $data => $value) {
                $info = $this->Infos->find()->where(['data' => $data])->first();
                $info->value = $value;
                $this->Infos->save($info);
            }
        }
        $message =  [
            'text' => __('La información fue guardada correctamente'),
            'type' => 'success',
        ];
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }

    /**
     * GetData method
     *
     * @param string|null $data.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function getData($data = null) {
        $data = $this->request->params['data'];
        
        $value = $this->Infos->find()
            ->where(['data' => $data])
            ->first()->value;
        
        $this->set(compact('value'));
        $this->set('_serialize', ['value']);
    }
    
    /**
     * GetDataMany method
     *
     * @param string|null $data.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function getDataMany($data = null) {
        $datas = $this->request->data;
        $info = array();
        
        if ($this->request->is('post')) {
            foreach ($datas as $data) {
                $value = $this->Infos->find()
                    ->where(['data' => $data])
                    ->first()->value;
                $info[$data] = $value;
            }
        }
        
        $this->set(compact('info'));
        $this->set('_serialize', ['info']);
    }
    
    public function getDataByData ($search = null) {
        $search = $this->request->data;
        
        $infos = $this->Infos->find()
            ->where(['Infos.data in ' => $search]);
        
        $this->set(compact('infos'));
        $this->set('_serialize', ['infos']);
    }
    
    public function previewFondo() {
        $this->viewBuilder()->layout(false);
        
        if ($this->request->is("post")) {
            $fondo = $this->request->data["file"];
            
            $path_dst = WWW_ROOT . "tmp" . DS;
            $ext = pathinfo($fondo['name'], PATHINFO_EXTENSION);
            $filename = 'fondo-' . $this->Random->randomString() . '.' . $ext;
           
            $filename_src = $fondo["tmp_name"];
            $file_src = new File($filename_src);

            if ($file_src->copy($path_dst . $filename)) {
                $code = 200;
                $message = 'El fondo fue guardado correctamente';
            } else {
                $message = "El fondo no fue subido con éxito";
            }
            
            $this->set(compact("code", "message", "filename"));
            $this->set("_serialize", ["message", "filename"]);
        }
    }
    
    public function saveFondo() {
        $fondo = $this->Infos->newEntity();
        if ($this->request->is('post')) {
            
            $fondo = $this->Infos->patchEntity($fondo, $this->request->data);
            
            if ($fondo->value) {
                $path_src = WWW_ROOT . "tmp" . DS;
                $file_src = new File($path_src . $fondo->value);
             
                $path_dst = WWW_ROOT . 'img' . DS . 'bg' . DS;
                $fondo->value = $this->Random->randomFileName($path_dst, 'fondo-', $file_src->ext());
                
                $file_src->copy($path_dst . $fondo->value);
            }
            
            if ($this->Infos->save($fondo)) {
                $code = 200;
                $message = 'El fondo fue guardado correctamente';
            } else {
                $message = 'El fondo no fue guardado correctamente';
            }
        }
        
        $this->set(compact('fondo', 'message', 'code'));
        $this->set('_serialize', ['fondo', 'message', 'code']);
    }
    
    public function download($id) {
        $info = $this->Infos->get($id);
        $file = WWW_ROOT . "files". DS . 'archivos' . DS . $info->value;
        $response = $this->response->withFile(
            $file,
            ['download' => true, 'name' => $info->data . '.pdf']
        );
        return $response;
    }
    
    public function previewFile() {
        $this->viewBuilder()->layout(false);
        
        if ($this->request->is("post")) {
            $brochure = $this->request->data["file"];
            
            $filename = "doc-" . $this->randomString();
            $url = WWW_ROOT . "tmp" . DS . $filename;
            $dst_final = WWW_ROOT . "files". DS . 'brochures' . DS . $filename;
                        
            while (file_exists($dst_final)) {
                $filename = "doc-" . $this->randomString();
                $url = WWW_ROOT . "tmp" . DS . $filename;
                $dst_final = WWW_ROOT . "files". DS . 'brochures' . DS . $filename;
            }
            
            if (move_uploaded_file($brochure["tmp_name"], $url)) {
                $message = [
                    "type" => "success",
                    "text" => "El brochure fue subida con éxito"
                ];
            } else {
                $message = [
                    "type" => "error",
                    "text" => "El brochure no fue subida con éxito",
                ];
            }
            
            $this->set(compact("message", "filename"));
            $this->set("_serialize", ["message", "filename"]);
        }
    }
}