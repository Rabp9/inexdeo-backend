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

        $this->belongsTo('Estados', [
            'foreignKey' => 'estado_id',
            'joinType' => 'INNER'
        ]);
        
        $this->hasMany('ServicioImages', [
            'foreignKey' => 'servicio_id'
        ]);
        
    }

     public function afterSave($event, $entity, $options) {
        $imageOperations = [
            'thumbnail' => [
                'height' => 600,
                'width' => 200
            ],
        ];
        
        $path = WWW_ROOT . "img". DS . 'servicios' . DS;
    
        $this->processImage($path . $entity->img_portada,
            $path . $entity->img_portada . '_thumb.png',
            [],
            $imageOperations
        );
        return;
    }
}
