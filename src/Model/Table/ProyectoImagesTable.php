<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProyectoImages Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Proyectos
 *
 * @method \App\Model\Entity\ProyectoImage get($primaryKey, $options = [])
 * @method \App\Model\Entity\ProyectoImage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ProyectoImage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ProyectoImage|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProyectoImage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ProyectoImage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ProyectoImage findOrCreate($search, callable $callback = null, $options = [])
 */
class ProyectoImagesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('proyecto_images');
        $this->displayField('url');
        $this->primaryKey('id');

        $this->belongsTo('Proyectos', [
            'foreignKey' => 'proyecto_id',
            'joinType' => 'INNER'
        ]);
    }
}
