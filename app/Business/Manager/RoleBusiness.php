<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 2018/4/12
 * Time: 上午11:43
 */

namespace Business\Manager;

use Core\Base\Business;
use Core\Tools\ArrayUtil;
use Core\Tools\ErrorHandler;
use Wrappers\ManagerRoleWrapper;
use Core\Tools\StringUtil;
use Models\ManagerPowerModel;
use Models\ManagerRoleModel;
use Wrappers\ManagerWrapper;

class RoleBusiness extends Business
{
    /**
     * 保存
     * @param ManagerRoleWrapper $wrapRole
     * @return bool
     */
    public function save(ManagerRoleWrapper $wrapRole)
    {
        $mRole = new ManagerRoleModel();
        if ($mRole->isRepeat('role_name', $wrapRole->role_name, (int)$wrapRole->id)) {
            ErrorHandler::setErrorInfo('角色名称重复！');
            return FALSE;
        }

        $powerList = '';
        $mManagerPower = ManagerPowerModel::find("id in ({$wrapRole->powers})");
        foreach ($mManagerPower as $value) {
            $powerList .= ',' . $value->id;
            $powerList .= ',' . $value->dependent;
        }
        $power = StringUtil::formatList($powerList);

        $wrapRole->powers = $power;

        $wrapRole->mappingToModel($mRole);
        $result = $mRole->save();
        if ($result === FALSE) {
            return FALSE;
        }
        return $mRole->toArray();
    }

    private function setChildMenuPower(array $allowMenu, $power)
    {
        $arrPath = ArrayUtil::toArray($power['path']);

        $val = &$allowMenu;
        foreach ($arrPath as $value) {
            if ($value != $power['id']) {
                $val = &$val[$value]['child'];
            }
        }
        $val[$power['id']] = $power;
        return $allowMenu;
    }


    private function sortMenu($menu)
    {
        foreach ($menu as &$value) {
            if (count($value['child']) > 1) {
                usort($value['child'], function ($a, $b) {
                    if ($a['sort'] == $b['sort']) return 0;
                    return ($a['sort'] < $b['sort']) ? -1 : 1;
                });
            }
        }
        return $menu;
    }

    /**
     * 获取多个角色的复合权限
     * @param $rolesIds
     * @return array
     */
    public function rolesPower($rolesIds)
    {
        $roles = ArrayUtil::toArray($rolesIds);
        if (!$roles) {
            return [];
        }

        $mRole = ManagerRoleModel::find(['id in (' . implode(',', $roles) . ')', 'order' => 'id']);
        if (!$mRole) {
            return [];
        }

        foreach ($mRole as $role) {
            if (strtolower($role->role) == 'system') {
                $mPowers = ManagerPowerModel::find('status = 1');
                break;
            }
            $powers = ArrayUtil::toArray($role->powers);
        }
        if (!isset($mPowers) && isset($powers)) {
            $mPowers = ManagerPowerModel::find(
                [
                    'id IN ({powers:array}) AND status = 1',
                    'bind' => ['powers' => $powers],
                ]
            );
        }

        $allows = [
            'menu' => [],
            'route' => [],
        ];
        foreach ($mPowers as $value) {
            if ($value->type == 1) {
                if ($value->parent_id == 0) {
                    $allows['menu'][$value->id] = $value->toArray();
                } else {
                    $allows['menu'] = $this->setChildMenuPower($allows['menu'], $value->toArray());
                }
            }
            if (!empty($value->target)) {
                $allows['route'][$value->target] = TRUE;
            }
        }

        $allows['menu'] = $this->sortMenu($allows['menu']);
        return $allows;
    }


    /**
     * 获取角色的权限
     * @param $roleId
     * @return array
     */
    public function rolePower($roleId)
    {
        $mRole = ManagerRoleModel::findFirst($roleId);
        //$mRole = new ManagerRoleModel();
        if (!$mRole) {
            return [];
        }

        if (strtolower($mRole->role) == 'system') {
            $mPowers = ManagerPowerModel::find('status = 1');
        } else {
            $mPowers = ManagerPowerModel::find(
                [
                    'id IN ({powers:array}) AND status = 1',
                    'bind' => ['powers' => ArrayUtil::toArray($mRole->powers)],
                ]
            );
        }

        $role = [
            'name' => $mRole->role_name,
            'menu' => [],
            'route' => [],
        ];
        foreach ($mPowers as $value) {
            if ($value->type == 1) {
                if ($value->parent_id == 0) {
                    $role['menu'][$value->id] = $value->toArray();
                } else {
                    $role['menu'] = $this->setChildMenuPower($role['menu'], $value->toArray());
                }
            }
            if (!empty($value->target)) {
                $role['route'][$value->target] = TRUE;
            }
        }


        $role['menu'] = $this->sortMenu($role['menu']);
        return $role;
    }

}