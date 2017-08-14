<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ServicioImages Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Servicios
 *
 * @method \App\Model\Entity\ServicioImage get($primaryKey, $options = [])
 * @method \App\Model\Entity\ServicioImage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ServicioImage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ServicioImage|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ServicioImage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ServicioImage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ServicioImage findOrCreate($search, callable $callback = null, $options = [])
 */
class ServicioImagesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('servicio_images');
        $this->displayField('url');
        $this->primaryKey('id');

        $this->belongsTo('Servicios', [
            'foreignKey' => 'servicio_id',
            'joinType' => 'INNER'
        ]);
    }
}
