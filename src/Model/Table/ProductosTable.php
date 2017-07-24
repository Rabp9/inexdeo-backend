<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Productos Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Estados
 * @property \Cake\ORM\Association\HasMany $ProductoImages
 *
 * @method \App\Model\Entity\Producto get($primaryKey, $options = [])
 * @method \App\Model\Entity\Producto newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Producto[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Producto|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Producto patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Producto[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Producto findOrCreate($search, callable $callback = null, $options = [])
 */
class ProductosTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);
        
        $this->table('productos');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Burzum/Imagine.Imagine');

        $this->belongsTo('Estados', [
            'foreignKey' => 'estado_id',
            'joinType' => 'INNER'
        ]);
        
        $this->hasMany('ProductoImages', [
            'foreignKey' => 'producto_id'
        ])->setDependent(true);
        
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
        
        $path = WWW_ROOT . "img". DS . 'productos' . DS;
        
        if ($entity->img_portada) {
            $ext = pathinfo($entity->img_portada, PATHINFO_EXTENSION);
            $filename_base = basename($entity->img_portada, '.' . $ext);
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
        if (sizeof($entity->producto_images) > 0) {
            foreach ($entity->producto_images as $producto_image) {
                $ext = pathinfo($producto_image->url, PATHINFO_EXTENSION);
                $filename_base = basename($producto_image->url, '.' . $ext);
                $this->processImage($path . $producto_image->url,
                    $path . $filename_base . '_large.' . $ext,
                    [],
                    $imageOperationsLarge
                );      
                $this->processImage($path . $producto_image->url,
                    $path . $filename_base . '_small.' . $ext,
                    [],
                    $imageOperationsSmall
                );
            }
        }
    }
}
