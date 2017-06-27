<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ControllerRoles Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Roles
 * @property \Cake\ORM\Association\BelongsTo $Controllers
 *
 * @method \App\Model\Entity\ControllerRole get($primaryKey, $options = [])
 * @method \App\Model\Entity\ControllerRole newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ControllerRole[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ControllerRole|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ControllerRole patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ControllerRole[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ControllerRole findOrCreate($search, callable $callback = null, $options = [])
 */
class ControllerRolesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('controller_roles');
        $this->entityClass('ControllerRol');
        $this->displayField('permiso');
        $this->primaryKey('id');

        $this->belongsTo('Roles', [
            'foreignKey' => 'rol_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Controllers', [
            'foreignKey' => 'controller_id',
            'joinType' => 'INNER'
        ]);
    }
}
