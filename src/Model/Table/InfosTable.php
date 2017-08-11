<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Infos Model
 *
 * @method \App\Model\Entity\Info get($primaryKey, $options = [])
 * @method \App\Model\Entity\Info newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Info[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Info|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Info patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Info[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Info findOrCreate($search, callable $callback = null, $options = [])
 */
class InfosTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->table('infos');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Burzum/Imagine.Imagine');
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
        
        $path = WWW_ROOT . "img". DS . 'bg' . DS;
        $bgs = array('bg_quienes_somos', 'bg_contactanos', 'bg_mision', 'bg_historia', 'bg_hero');
                
        if (in_array($entity->data, $bgs)) {
            $ext = pathinfo($entity->value, PATHINFO_EXTENSION);
            $filename_base = basename($entity->value, '.' . $ext);
            if (file_exists($path . $entity->value)) {
                $this->processImage($path . $entity->value,
                    $path . $filename_base . '_large.' . $ext,
                    [],
                    $imageOperationsLarge
                );
                $this->processImage($path . $entity->value,
                    $path . $filename_base . '_small.' . $ext,
                    [],
                    $imageOperationsSmall
                );
            }
        }
    }
}
