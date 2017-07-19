<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Filesystem\File;

/**
 * Productos Controller
 *
 * @property \App\Model\Table\ProductosTable $Productos
 */
class ProductosController extends AppController
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
        $productos = $this->Productos->find()
            ->where(['estado_id' => 1])
            ->contain('ProductoImages');
        
        $this->set(compact('productos'));
        $this->set('_serialize', ['productos']);
    }
    
    /**
     * Get Admin method
     *
     * @return \Cake\Network\Response|null
     */
    public function getAdmin() {
        $this->viewBuilder()->layout(false);
        
        $productos = $this->Productos->find();
                
        $this->set(compact('productos'));
        $this->set('_serialize', ['productos']);
    }
    
    public function view($id) {
        $producto = $this->Productos->get($id, [
            'contain' => 'ProductoImages'
        ]);
        
        $this->set(compact('producto'));
        $this->set('_serialize', ['producto']);
    }
    
    public function getRandom($num = null) {
        $num = $this->request->param('num');
        
        $productos = $this->Productos->find()
            ->where(['estado_id' => 1])
            ->limit($num)
            ->order('rand()');
        
        $this->set(compact('productos'));
        $this->set('_serialize', ['productos']);
    }
    
    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $producto = $this->Productos->newEntity();
        
        if ($this->request->is('post')) {
            $producto = $this->Productos->patchEntity($producto, $this->request->data);
            $producto->estado_id = 1;
            
            if ($producto->img_portada) {
                $path_src = WWW_ROOT . "tmp" . DS;
                $file_tmp = new File($path_src . $producto->img_portada);
             
                $path_dst = WWW_ROOT . 'img' . DS . 'productos' . DS;
                $producto->img_portada = $this->Random->randomFileName($path_dst, 'producto-');
                
                $file_tmp->copy($path_dst . $producto->img_portada);
            }
            
            if ($producto->brochure) {
                // Brochure
                $dst_brochure = WWW_ROOT . "files". DS . 'brochures' . DS . $producto->brochure;
                $src_brochure = WWW_ROOT . "tmp" . DS . $producto->brochure;
            }
            
            if ($this->Productos->save($producto)) {
                // move file
                
                if ($producto->brochure) {
                    if (file_exists($src_brochure)) {
                        rename($src_brochure, $dst_brochure);
                    }
                }
                
                foreach ($producto->producto_images as $producto_image) {
                    $src = WWW_ROOT . "tmp" . DS . $producto_image->url;
                    $dst = WWW_ROOT . "img". DS . 'productos' . DS . $producto_image->url;
                    if (file_exists($src)) {
                        rename($src, $dst);
                    }
                }
                $code = 200;
                $message = 'El producto fue guardado correctamente';
            } else {
                $message = 'El producto no fue guardado correctamente';
            }
        }
        
        $this->set(compact('producto', 'message'));
        $this->set('_serialize', ['producto', 'message']);
    }
    
    public function preview() {
        $this->viewBuilder()->layout(false);
        
        if ($this->request->is("post")) {
            $filenames = array();
            $images = $this->request->data["files"];
            
            foreach ($images as $image) {
                $filename = "producto-" . $this->randomString();
                $url = WWW_ROOT . "tmp" . DS . $filename;
                $dst_final = WWW_ROOT . "img". DS . 'productos' . DS . $filename;
                
                while (file_exists($dst_final)) {
                    $filename = "producto-" . $this->randomString();
                    $url = WWW_ROOT . "tmp" . DS . $filename;
                    $dst_final = WWW_ROOT . "img". DS . 'productos' . DS . $filename;
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
     * @author	XEWeb <>
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
     * @author	XEWeb <>
     * @param $length the length of the string to create
     * @return $str the string
     */
    public function deleteImage() {
        $id = $this->request->getData()['id'];
        
        $producto_image = $this->Productos->ProductoImages->get($id);
        if ($this->Productos->ProductoImages->delete($producto_image)) {
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
            
            $filename = "producto-" . $this->randomString();
            $url = WWW_ROOT . "tmp" . DS . $filename;
            $dst_final = WWW_ROOT . "img". DS . 'productos' . DS . $filename;
            
            while (file_exists($dst_final)) {
                $filename = "producto-" . $this->randomString();
                $url = WWW_ROOT . "tmp" . DS . $filename;
                $dst_final = WWW_ROOT . "img". DS . 'productos' . DS . $filename;
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
        $producto = $this->Productos->get($id);
        $file = WWW_ROOT . "files". DS . 'brochures' . DS . $producto->brochure;
        $response = $this->response->withFile(
            $file,
            ['download' => true, 'name' => $producto->title . '.pdf']
        );
        return $response;
    }
    
    public function remove() {
        $producto = $this->Productos->get($this->request->getData('id'));
        
        if ($this->Productos->delete($producto)) {
            $message = [
                "type" => "success",
                "text" => "El producto fue eliminado con éxito"
            ];
        } else {
            $message = [
                "type" => "error",
                "text" => "El producto no fue eliminado con éxito",
            ];
        }
        
        $this->set(compact("message"));
    }
    
}
