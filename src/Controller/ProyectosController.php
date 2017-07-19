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

    public function getAdmin() {
        $this->viewBuilder()->layout(false);
        
        $proyectos = $this->Proyectos->find();
                
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

    public function add() {
        $this->viewBuilder()->layout(false);
        $proyecto = $this->Proyectos->newEntity();
        $proyecto->estado_id = 1;
        
        if ($this->request->is('post')) {
            
            $proyecto = $this->Proyectos->patchEntity($proyecto, $this->request->data);
            
            if ($proyecto->brochure) {
                // Brochure
                $dst_brochure = WWW_ROOT . "files". DS . 'brochures' . DS . $proyecto->brochure;
                $src_brochure = WWW_ROOT . "tmp" . DS . $proyecto->brochure;
            }
            
            if ($proyecto->img_portada) {
                // Brochure
                $dst_portada = WWW_ROOT . "img". DS . 'proyectos' . DS . $proyecto->img_portada;
                $src_portada = WWW_ROOT . "tmp" . DS . $proyecto->img_portada;
            }

            if ($proyecto->img_portada) {
                if (file_exists($src_portada)) {
                    rename($src_portada, $dst_portada);
                }
            }

            if ($this->Proyectos->save($proyecto)) {
                // move file
                
                if ($proyecto->brochure) {
                    if (file_exists($src_brochure)) {
                        rename($src_brochure, $dst_brochure);
                    }
                }
                
                foreach ($proyecto->proyecto_images as $proyecto_image) {
                    $src = WWW_ROOT . "tmp" . DS . $proyecto_image->url;
                    $dst = WWW_ROOT . "img". DS . 'proyectos' . DS . $proyecto_image->url;
                    if (file_exists($src)) {
                        rename($src, $dst);
                    }
                }             
                
                $message =  [
                    'text' => __('El proyecto fue guardado correctamente'),
                    'type' => 'success',
                ];
            } else {
                $message =  [
                    'text' => __('El proyecto no fue guardado correctamente'),
                    'type' => 'error',
                ];
            }
        }
        $this->set(compact('proyecto', 'message'));
        $this->set('_serialize', ['proyecto', 'message']);
    }
    
    public function preview() {
        $this->viewBuilder()->layout(false);
        
        if ($this->request->is("post")) {
            $filenames = array();
            $images = $this->request->data["files"];
            
            foreach ($images as $image) {
                $filename = "proyecto-" . $this->randomString();
                $url = WWW_ROOT . "tmp" . DS . $filename;
                $dst_final = WWW_ROOT . "img". DS . 'proyectos' . DS . $filename;
                
                while (file_exists($dst_final)) {
                    $filename = "proyecto-" . $this->randomString();
                    $url = WWW_ROOT . "tmp" . DS . $filename;
                    $dst_final = WWW_ROOT . "img". DS . 'proyectos' . DS . $filename;
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
        
        $proyecto_image = $this->Proyectos->ProyectoImages->get($id);
        if ($this->Proyectos->ProyectoImages->delete($proyecto_image)) {
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
            
            $filename = "proyecto-" . $this->randomString();
            $url = WWW_ROOT . "tmp" . DS . $filename;
            $dst_final = WWW_ROOT . "img". DS . 'proyectos' . DS . $filename;
                        
            while (file_exists($dst_final)) {
                $filename = "proyecto-" . $this->randomString();
                $url = WWW_ROOT . "tmp" . DS . $filename;
                $dst_final = WWW_ROOT . "img". DS . 'proyectos' . DS . $filename;
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
        $proyecto = $this->Proyectos->get($id);
        $file = WWW_ROOT . "files". DS . 'brochures' . DS . $proyecto->brochure;
        $response = $this->response->withFile(
            $file,
            ['download' => true, 'name' => $proyecto->title . '.pdf']
        );
        return $response;
    }
    
    public function remove() {
        $proyecto = $this->Proyectos->get($this->request->getData('id'));
        
        if ($this->Proyectos->delete($proyecto)) {
            $message = [
                "type" => "success",
                "text" => "El proyecto fue eliminado con éxito"
            ];
        } else {
            $message = [
                "type" => "error",
                "text" => "El proyecto no fue eliminado con éxito",
            ];
        }
        
        $this->set(compact("message"));
    }
}
