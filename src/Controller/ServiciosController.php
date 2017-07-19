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
     public function getAdmin() {
        $this->viewBuilder()->layout(false);
        
        $servicios = $this->Servicios->find();
                
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

    public function add() {
        $this->viewBuilder()->layout(false);
        $servicio = $this->Servicios->newEntity();
        $servicio->estado_id = 1;
        
        if ($this->request->is('post')) {
            
            $servicio = $this->Servicios->patchEntity($servicio, $this->request->data);
            
            if ($servicio->brochure) {
                // Brochure
                $dst_brochure = WWW_ROOT . "files". DS . 'brochures' . DS . $servicio->brochure;
                $src_brochure = WWW_ROOT . "tmp" . DS . $servicio->brochure;
            }
            
            if ($servicio->img_portada) {
                // Brochure
                $dst_portada = WWW_ROOT . "img". DS . 'servicios' . DS . $servicio->img_portada;
                $src_portada = WWW_ROOT . "tmp" . DS . $servicio->img_portada;
            }

            if ($servicio->img_portada) {
                if (file_exists($src_portada)) {
                    rename($src_portada, $dst_portada);
                }
            }

            if ($this->Servicios->save($servicio)) {
                // move file
                
                if ($servicio->brochure) {
                    if (file_exists($src_brochure)) {
                        rename($src_brochure, $dst_brochure);
                    }
                }
                
                foreach ($servicio->servicio_images as $servicio_image) {
                    $src = WWW_ROOT . "tmp" . DS . $servicio_image->url;
                    $dst = WWW_ROOT . "img". DS . 'servicios' . DS . $servicio_image->url;
                    if (file_exists($src)) {
                        rename($src, $dst);
                    }
                }             
                
                $message =  [
                    'text' => __('El servicio fue guardado correctamente'),
                    'type' => 'success',
                ];
            } else {
                $message =  [
                    'text' => __('El servicio no fue guardado correctamente'),
                    'type' => 'error',
                ];
            }
        }
        $this->set(compact('servicio', 'message'));
        $this->set('_serialize', ['servicio', 'message']);
    }
    
    public function preview() {
        $this->viewBuilder()->layout(false);
        
        if ($this->request->is("post")) {
            $filenames = array();
            $images = $this->request->data["files"];
            
            foreach ($images as $image) {
                $filename = "servicio-" . $this->randomString();
                $url = WWW_ROOT . "tmp" . DS . $filename;
                $dst_final = WWW_ROOT . "img". DS . 'servicios' . DS . $filename;
                
                while (file_exists($dst_final)) {
                    $filename = "servicio-" . $this->randomString();
                    $url = WWW_ROOT . "tmp" . DS . $filename;
                    $dst_final = WWW_ROOT . "img". DS . 'servicios' . DS . $filename;
                }

                if (move_uploaded_file($image["tmp_name"], $url)) {
                    $filenames[] = $filename;
                } else {
                    $message = [
                        "type" => "error",
                        'text' => 'Algunas imágenes no pudieron ser cargadas correctamente'
                    ];
                }
            }
            $this->set(compact("message", "filenames"));
            $this->set("_serialize", ["message", "filenames"]);
        }
    }
    
    /*
     * Create a random string
     * @author  XEWeb <>
     * @param $length the length of the string to create
     * @return $str the string
     */
    private function randomString($length = 6) {
        $str = "";
        $characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }
    
    /*
     * Delete Image
     * @author  XEWeb <>
     * @param $length the length of the string to create
     * @return $str the string
     */
    public function deleteImage() {
        $id = $this->request->getData()['id'];
        
        $servicio_image = $this->Servicios->ServicioImages->get($id);
        if ($this->Servicios->ServicioImages->delete($servicio_image)) {
            $message =  [
                'text' => __('La imagen fue eliminada correctamente'),
                'type' => 'success',
            ];
        } else {
            $message =  [
                'text' => __('La imagen no fue eliminada correctamente'),
                'type' => 'error',
            ];
        }
        $this->set(compact("message"));
        $this->set("_serialize", ["message"]);
    }
    
    public function previewBrochure() {
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
    
    public function previewPortada() {
        $this->viewBuilder()->layout(false);
        
        if ($this->request->is("post")) {
            $portada = $this->request->data["file"];
            
            $filename = "servicio-" . $this->randomString();
            $url = WWW_ROOT . "tmp" . DS . $filename;
            $dst_final = WWW_ROOT . "img". DS . 'servicios' . DS . $filename;
                        
            while (file_exists($dst_final)) {
                $filename = "servicio-" . $this->randomString();
                $url = WWW_ROOT . "tmp" . DS . $filename;
                $dst_final = WWW_ROOT . "img". DS . 'servicios' . DS . $filename;
            }
            
            if (move_uploaded_file($portada["tmp_name"], $url)) {
                $message = [
                    "type" => "success",
                    "text" => "La portada fue subida con éxito"
                ];
            } else {
                $message = [
                    "type" => "error",
                    "text" => "La portada no fue subida con éxito",
                ];
            }
            
            $this->set(compact("message", "filename"));
            $this->set("_serialize", ["message", "filename"]);
        }
    }
    
    public function download($id) {
        $servicio = $this->Servicios->get($id);
        $file = WWW_ROOT . "files". DS . 'brochures' . DS . $servicio->brochure;
        $response = $this->response->withFile(
            $file,
            ['download' => true, 'name' => $servicio->title . '.pdf']
        );
        return $response;
    }
    
    public function remove() {
        $servicio = $this->Servicios->get($this->request->getData('id'));
        
        if ($this->Servicios->delete($servicio)) {
            $message = [
                "type" => "success",
                "text" => "El servicio fue eliminado con éxito"
            ];
        } else {
            $message = [
                "type" => "error",
                "text" => "El servicio no fue eliminado con éxito",
            ];
        }
        
        $this->set(compact("message"));
    }
}
