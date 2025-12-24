<?php

namespace App\Classes\BusinessManagement;

use App\Models\Setting\Module;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SetMenu
{
    public static function setDaftarMenu($idUser)
    {
        $dataMenu = array();

        $dataIndex = [
            'title' => 'Dashboard',
            'root' => true,
            'icon' => 'flaticon-home', // or can be 'flaticon-home' or any flaticon-*
            'page' => '/',
            'new-tab' => false
        ];

        array_push($dataMenu, $dataIndex);

        $parentMenu = Module::join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select(
                                    'module.id',
                                    'module.menu',
                                    'module.url',
                                    'module.parent',
                                    'module.menu_icon'
                                )
                                ->where([
                                    ['module.parent', '=', 'main'],
                                    ['module.active', '=', 'Y'],
                                    //['module_access.user_id', '=', $idUser],
                                ])
                                ->orderBy('module.order_number')
                                ->get();
                                //dd($parentMenu);
        foreach ($parentMenu as $dataParent) {
            $dataParentTemp = Module::join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select(
                                        DB::raw("module.menu AS title"),
                                        DB::raw("module.url AS page")
                                    )
                                    ->where([
                                        ['module.parent', '=', $dataParent->id],
                                        ['module_access.user_id', '=', $idUser],
                                        ['module.active', '=', 'Y'],
                                        ['module_access.active', '=', 'Y']
                                    ])
                                    ->orderBy('module.order_number')
                                    ->get();
                                   // dd(count($dataParentTemp));
            if (count($dataParentTemp) > 0) {
                $dataSubMenu = [
                    'title' => ucwords($dataParent->menu),
                    'icon' => $dataParent->menu_icon,
                    'bullet' => 'dot',
                    'page' => ucwords($dataParent->menu),
                    'root' => true,
                    'submenu' => json_decode($dataParentTemp,true)
                ];
                //dd($dataMenu, $dataSubMenu);
                array_push($dataMenu, $dataSubMenu);
            }
        }
        //dd($dataMenu);
        Config::set('menu_aside.items', $dataMenu);
    }

    public static function setDaftarMenuHeader($idUser)
    {
        $dataMenu = array();

        $dataIndex = [];

        array_push($dataMenu, $dataIndex);

        $parentMenu = Module::join('module_access', 'module_access.menu_id', '=', 'module.id')
                                ->select(
                                    'module.id',
                                    'module.menu',
                                    'module.url',
                                    'module.parent',
                                    'module.menu_icon'
                                )
                                ->where([
                                    ['module.parent', '=', 'header'],
                                    ['module.active', '=', 'Y'],
                                    //['module_access.user_id', '=', $idUser],
                                ])
                                ->orderBy('module.order_number')
                                ->get();
                                //dd($parentMenu);
        foreach ($parentMenu as $dataParent) {
            $dataParentTemp = Module::join('module_access', 'module_access.menu_id', '=', 'module.id')
                                    ->select(
                                        DB::raw("module.menu AS title"),
                                        DB::raw("module.url AS page"),
                                        DB::raw("'classic' AS type"),
                                        DB::raw("module.menu_icon AS icon"),
                                        DB::raw("'left' AS alignment")
                                    )
                                    ->where([
                                        ['module.parent', '=', $dataParent->id],
                                        ['module_access.user_id', '=', $idUser],
                                        ['module.active', '=', 'Y'],
                                        ['module_access.active', '=', 'Y']
                                    ])
                                    ->orderBy('module.order_number')
                                    ->get();
                                   // dd(count($dataParentTemp));
            if (count($dataParentTemp) > 0) {
                $dataSubMenu = [
                    'title' => ucwords($dataParent->menu),
                    'root' => true,
                    'toggle' => 'click',
                    'submenu' => [
                        'type' => 'classic',
                        'alignment' => 'left',
                        'items' => [
                            json_decode($dataParentTemp,true),
                        ]
                    ]
                ];
                //dd($dataMenu, $dataSubMenu);
                array_push($dataMenu, $dataSubMenu);
            }
        }
        //dd($dataMenu);
        Config::set('menu_header.items', $dataMenu);
    }
}
