<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Filesystem\File;

/**
 * Servicios Controller
 *
 * @property \App\Model\Table\ServiciosTable $Servicios
 */
class ServiciosController extends AppController
{
    public function initialize() {
        parent::initialize();
        $this->Auth->allow(['getRandom', 'index', 'view', 'download']);
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
        $servicio = $this->Servicios->newEntity();
        
        if ($this->request->is('post')) {
            $servicio = $this->Servicios->patchEntity($servicio, $this->request->data);
            $servicio->estado_id = 1;
            
            if ($servicio->img_portada) {
                $path_src = WWW_ROOT . "tmp" . DS;
                $file_src = new File($path_src . $servicio->img_portada);
             
                $path_dst = WWW_ROOT . 'img' . DS . 'servicios' . DS;
                $servicio->img_portada = $this->Random->randomFileName($path_dst, 'servicio-', $file_src->ext());
                
                $file_src->copy($path_dst . $servicio->img_portada);
            }
            
            if ($servicio->brochure) {
                // Brochure
                $dst_brochure = WWW_ROOT . "files". DS . 'brochures' . DS . $servicio->brochure;
                $src_brochure = WWW_ROOT . "tmp" . DS . $servicio->brochure;
                if (file_exists($src_brochure)) {
                    rename($src_brochure, $dst_brochure);
                }
            }
            
            foreach ($servicio->servicio_images as $k_image => $servicio_image) {
                if (!isset($servicio_image->id)) {
                    $path_src = WWW_ROOT . "tmp" . DS;
                    $file_src = new File($path_src . $servicio_image->url);
                    $path_dst = WWW_ROOT . 'img' . DS . 'servicios' . DS;
                    $servicio->servicio_images[$k_image]->url = $this->Random->randomFileName($path_dst, 'servicio-', $file_src->ext());
                    $file_src->copy($path_dst . $servicio->servicio_images[$k_image]->url);
                }
            }
            if ($this->Servicios->save($servicio)) {
                $code = 200;
                $message = 'El servicio fue guardado correctamente';
            } else {
                $message = 'El servicio no fue guardado correctamente';
            }
        }
        
        $this->set(compact('servicio', 'message', 'code'));
        $this->set('_serialize', ['servicio', 'message', 'code']);
    }
    
    public function preview() {
        $this->viewBuilder()->layout(false);
        
        if ($this->request->is("post")) {
            $filenames = array();
            $images = $this->request->data["files"];
            
            foreach ($images as $image) {
                $path_dst = WWW_ROOT . "tmp" . DS;
                $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
                $filename = 'servicio-' . $this->Random->randomString() . '.' . $ext;

                $filename_src = $image["tmp_name"];
                $file_src = new File($filename_src);

                if ($file_src->copy($path_dst . $filename)) {
                    $filenames[] = $filename;
                } else {
                    $message = 'Algunas imágenes no pudieron ser cargadas';
                }
            }
            $message = 'Todas las imágenes fueron cargadas';
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
            
            $path_dst = WWW_ROOT . "tmp" . DS;
            $ext = pathinfo($portada['name'], PATHINFO_EXTENSION);
            $filename = 'servicio-' . $this->Random->randomString() . '.' . $ext;
           
            $filename_src = $portada["tmp_name"];
            $file_src = new File($filename_src);

            if ($file_src->copy($path_dst . $filename)) {
                $code = 200;
                $message = 'El servicio fue guardado correctamente';
            } else {
                $message = "La portada no fue subida con éxito";
            }
            
            $this->set(compact("code", "message", "filename"));
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
