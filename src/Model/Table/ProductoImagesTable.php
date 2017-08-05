<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProductoImages Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Productos
 *
 * @method \App\Model\Entity\ProductoImage get($primaryKey, $options = [])
 * @method \App\Model\Entity\ProductoImage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ProductoImage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ProductoImage|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProductoImage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ProductoImage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ProductoImage findOrCreate($search, callable $callback = null, $options = [])
 */
class ProductoImagesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('producto_images');
        $this->displayField('url');
        $this->primaryKey('id');

        $this->belongsTo('Productos', [
            'foreignKey' => 'producto_id',
            'joinType' => 'INNER'
        ]);
    }
}
