<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Filesystem\File;

/**
 * Proyectos Controller
 *
 * @property \App\Model\Table\ProyectosTable $Proyectos
 */
class ProyectosController extends AppController
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
        $proyecto = $this->Proyectos->newEntity();
        
        if ($this->request->is('post')) {
            $proyecto = $this->Proyectos->patchEntity($proyecto, $this->request->data);
            $proyecto->estado_id = 1;
            
            if ($proyecto->img_portada) {
                $path_src = WWW_ROOT . "tmp" . DS;
                $file_src = new File($path_src . $proyecto->img_portada);
             
                $path_dst = WWW_ROOT . 'img' . DS . 'proyectos' . DS;
                $proyecto->img_portada = $this->Random->randomFileName($path_dst, 'proyecto-', $file_src->ext());
                
                $file_src->copy($path_dst . $proyecto->img_portada);
            }
            
            if ($proyecto->brochure) {
                // Brochure
                $dst_brochure = WWW_ROOT . "files". DS . 'brochures' . DS . $proyecto->brochure;
                $src_brochure = WWW_ROOT . "tmp" . DS . $proyecto->brochure;
                if (file_exists($src_brochure)) {
                    rename($src_brochure, $dst_brochure);
                }
            }
            
            foreach ($proyecto->proyecto_images as $k_image => $proyecto_image) {
                if (!isset($proyecto_image->id)) {
                    $path_src = WWW_ROOT . "tmp" . DS;
                    $file_src = new File($path_src . $proyecto_image->url);
                    $path_dst = WWW_ROOT . 'img' . DS . 'proyectos' . DS;
                    $proyecto->proyecto_images[$k_image]->url = $this->Random->randomFileName($path_dst, 'proyecto-', $file_src->ext());
                    $file_src->copy($path_dst . $proyecto->proyecto_images[$k_image]->url);
                }
            }
            if ($this->Proyectos->save($proyecto)) {
                $code = 200;
                $message = 'El proyecto fue guardado correctamente';
            } else {
                $message = 'El proyecto no fue guardado correctamente';
            }
        }
        
        $this->set(compact('proyecto', 'message', 'code'));
        $this->set('_serialize', ['proyecto', 'message', 'code']);
    }
    
    public function preview() {
        $this->viewBuilder()->layout(false);
        
        if ($this->request->is("post")) {
            $filenames = array();
            $images = $this->request->data["files"];
            
            foreach ($images as $image) {
                $path_dst = WWW_ROOT . "tmp" . DS;
                $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
                $filename = 'proyecto-' . $this->Random->randomString() . '.' . $ext;

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
            
            $path_dst = WWW_ROOT . "tmp" . DS;
            $ext = pathinfo($portada['name'], PATHINFO_EXTENSION);
            $filename = 'proyecto-' . $this->Random->randomString() . '.' . $ext;
           
            $filename_src = $portada["tmp_name"];
            $file_src = new File($filename_src);

            if ($file_src->copy($path_dst . $filename)) {
                $code = 200;
                $message = 'El proyecto fue guardado correctamente';
            } else {
                $message = "La portada no fue subida con éxito";
            }
            
            $this->set(compact("code", "message", "filename"));
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
