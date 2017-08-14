<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Servicios Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Estados
 * @property \Cake\ORM\Association\HasMany $ServicioImages
 *
 * @method \App\Model\Entity\Servicio get($primaryKey, $options = [])
 * @method \App\Model\Entity\Servicio newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Servicio[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Servicio|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Servicio patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Servicio[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Servicio findOrCreate($search, callable $callback = null, $options = [])
 */
class ServiciosTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('servicios');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Burzum/Imagine.Imagine');

        $this->belongsTo('Estados', [
            'foreignKey' => 'estado_id',
            'joinType' => 'INNER'
        ]);
        
        $this->hasMany('ServicioImages', [
            'foreignKey' => 'servicio_id'
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
        
        $path = WWW_ROOT . "img". DS . 'servicios' . DS;
        
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
        if (sizeof($entity->servicio_images) > 0) {
            foreach ($entity->servicio_images as $servicio_image) {
                $ext = pathinfo($servicio_image->url, PATHINFO_EXTENSION);
                $filename_base = basename($servicio_image->url, '.' . $ext);
                
                if (file_exists($path . $servicio_image->url)) {
                    $this->processImage($path . $servicio_image->url,
                        $path . $filename_base . '_large.' . $ext,
                        [],
                        $imageOperationsLarge
                    );      
                    $this->processImage($path . $servicio_image->url,
                        $path . $filename_base . '_small.' . $ext,
                        [],
                        $imageOperationsSmall
                    );
                }
            }
        }
    }
}
