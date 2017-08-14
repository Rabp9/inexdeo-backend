<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


/**
 * Albumes Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Estados
 * @property \Cake\ORM\Association\HasMany $Imagenes
 *
 * @method \App\Model\Entity\Album get($primaryKey, $options = [])
 * @method \App\Model\Entity\Album newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Album[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Album|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Album patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Album[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Album findOrCreate($search, callable $callback = null, $options = [])
 */
class AlbumesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('albumes');
        $this->displayField('descripcion');
        $this->primaryKey('id');

        $this->belongsTo('Estados', [
            'foreignKey' => 'estado_id',
            'joinType' => 'INNER'
        ]);
        
        $this->hasMany('Imagenes', [
            'foreignKey' => 'album_id'
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
        
        $path = WWW_ROOT . "img". DS . 'albumes' . DS;
        
        if (sizeof($entity->imagenes) > 0) {
            foreach ($entity->imagenes as $imagen) {
                $ext = pathinfo($imagen->url, PATHINFO_EXTENSION);
                $filename_base = basename($imagen->url, '.' . $ext);
                
                if (file_exists($path . $imagen->url)) {
                    $this->processImage($path . $imagen->url,
                        $path . $filename_base . '_large.' . $ext,
                        [],
                        $imageOperationsLarge
                    );      
                    $this->processImage($path . $imagen->url,
                        $path . $filename_base . '_small.' . $ext,
                        [],
                        $imageOperationsSmall
                    );
                }
            }
        }
    }
}
