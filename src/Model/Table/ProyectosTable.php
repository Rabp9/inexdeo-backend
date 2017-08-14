<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Proyectos Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Estados
 * @property \Cake\ORM\Association\HasMany $ProyectoImages
 *
 * @method \App\Model\Entity\Proyecto get($primaryKey, $options = [])
 * @method \App\Model\Entity\Proyecto newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Proyecto[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Proyecto|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Proyecto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Proyecto[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Proyecto findOrCreate($search, callable $callback = null, $options = [])
 */
class ProyectosTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('proyectos');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Burzum/Imagine.Imagine');

        $this->belongsTo('Estados', [
            'foreignKey' => 'estado_id',
            'joinType' => 'INNER'
        ]);
        
        $this->hasMany('ProyectoImages', [
            'foreignKey' => 'proyecto_id'
        ]);
        
    }

    public function afterSave($event, $entity, $options) {
        $imageOperationsLarge = [
            'thumbnail' => [
                'height' => 800,
                'width' => 800
            ],
        ];
        $imageOperationsSmall = [
            'thumbnail' => [
                'height' => 400,
                'width' => 400
            ],
        ];
        
        $path = WWW_ROOT . "img". DS . 'proyectos' . DS;
        
        if ($entity->img_portada) {
            $ext = pathinfo($entity->img_portada, PATHINFO_EXTENSION);
            $filename_base = basename($entity->img_portada, '.' . $ext);
            if (file_exists($path . $entity->img_portada)) {
                $this->processImage($path . $entity->img_portada,
                    $path . $filename_base . '_large.' . $ext,
                    [],
                    $imageOperationsLarge
                );
                $this->processImage($path . $entity->img_portada,
                    $path . $filename_base . '_small.' . $ext,
                    [],
                    $imageOperationsSmall
                );
            }
        }
        if (sizeof($entity->proyecto_images) > 0) {
            foreach ($entity->proyecto_images as $proyecto_image) {
                $ext = pathinfo($proyecto_image->url, PATHINFO_EXTENSION);
                $filename_base = basename($proyecto_image->url, '.' . $ext);
                
                if (file_exists($path . $proyecto_image->url)) {
                    $this->processImage($path . $proyecto_image->url,
                        $path . $filename_base . '_large.' . $ext,
                        [],
                        $imageOperationsLarge
                    );      
                    $this->processImage($path . $proyecto_image->url,
                        $path . $filename_base . '_small.' . $ext,
                        [],
                        $imageOperationsSmall
                    );
                }
            }
        }
    }
}
