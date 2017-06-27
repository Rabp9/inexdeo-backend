<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Hash;
use Cake\Collection\Collection;

/**
 * Rol Entity
 *
 * @property int $id
 * @property string $descripcion
 * @property int $estado_id
 *
 * @property \App\Model\Entity\Estado $estado
 */
class Rol extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];
    
    protected $_virtual = ['permisos'];
    
    protected function _getPermisos() {
        if (isset($this->_properties['controller_roles'])) {
            $controller_roles = $this->_properties['controller_roles'];

            $permisos = [];
            foreach ($controller_roles as $controller_rol) {
                if ($controller_rol->permiso) {
                    $permisos[] = $controller_rol;
                }
            }
            $arr_permisos = Hash::extract($permisos, '{n}.controller.controller_name');
            $arr_permisos = new Collection($arr_permisos);
            $str_permisos = $arr_permisos->reduce(function ($string, $permiso) {
                return $string . $permiso . ', ';
            }, '');
            return $str_permisos;
        }
        return '';
    }
}
