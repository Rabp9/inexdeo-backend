<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Filesystem\File;

/**
 * Albumes Controller
 *
 * @property \App\Model\Table\ProductosTable $Albumes
 */
class AlbumesController extends AppController
{
    public function initialize() {
        parent::initialize();
        $this->Auth->allow(['index', 'view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index() {
        $albumes = $this->Albumes->find()
            ->where(['estado_id' => 1])
            ->contain('Imagenes');
        
        $this->set(compact('albumes'));
        $this->set('_serialize', ['albumes']);
    }
    
    public function view($id) {
        $album = $this->Albumes->get($id, [
            'contain' => 'Imagenes'
        ]);
        
        $this->set(compact('album'));
        $this->set('_serialize', ['album']);
    }

    public function getAdmin() {
        $this->viewBuilder()->layout(false);
        
        $albumes = $this->Albumes->find();
                
        $this->set(compact('albumes'));
        $this->set('_serialize', ['albumes']);
    }
    
    public function add() {
       $album = $this->Albumes->newEntity();
        
        if ($this->request->is('post')) {
            $album = $this->Albumes->patchEntity($album, $this->request->data);
            $album->estado_id = 1;
            
            debug($album);
            
            foreach ($album->imagenes as $k_image =>$imagen) {
                if (!isset($imagen->id)) {
                    $path_src = WWW_ROOT . "tmp" . DS;
                    $file_src = new File($path_src .$imagen->url);
                    $path_dst = WWW_ROOT . 'img' . DS . 'galeria' . DS;
                    $album->imagenes[$k_image]->url = $this->Random->randomFileName($path_dst, 'album-', $file_src->ext());
                    $album->imagenes[$k_image]->estado_id = 1;
                    $file_src->copy($path_dst .$album->imagenes[$k_image]->url);
                }
            }
            if ($this->Albumes->save($album)) {
                $code = 200;
                $message = 'El album fue guardado correctamente';
            } else {
                $message = 'El album no pudo ser guardado';
            }
        }
        
        $this->set(compact('album', 'message', 'code'));
        $this->set('_serialize', ['album', 'message', 'code']);
    }

    public function preview() {
        $this->viewBuilder()->layout(false);
        
        if ($this->request->is("post")) {
            $filenames = array();
            $images = $this->request->data["files"];
            
            foreach ($images as $image) {
                $path_dst = WWW_ROOT . "tmp" . DS;
                $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
                $filename = 'album-' . $this->Random->randomString() . '.' . $ext;

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

    public function deleteImage() {
        $id = $this->request->getData()['id'];
        
        $imagenes = $this->Albumes->Imagenes->get($id);
        if ($this->Albumes->Imagenes->delete($imagenes)) {
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

    public function remove() {
        $album = $this->Albumes->get($this->request->getData('id'));
        
        if ($this->Albumes->delete($album)) {
            $message = [
                "type" => "success",
                "text" => "El Álbum fue eliminado con éxito"
            ];
        } else {
            $message = [
                "type" => "error",
                "text" => "El Álbum no fue eliminado con éxito",
            ];
        }
        
        $this->set(compact("message"));
    }
    
}
