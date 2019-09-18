<?php

function getTree($data = [], $treeKey = ['id'=>'id', 'title'=>'title', 'pid'=>'pid'])
{
    if (empty($data)) {
        return [];
    }
    $arr = [];
    foreach ($data as $key => &$value) {
        $value['value'] = $value[$treeKey['id']];
        $value['label'] = $value[$treeKey['title']];
        if ($value[$treeKey['pid']] == 0) {
            array_push($arr, $value);
            unset($value);
        }
    }

    foreach ($data as $key => &$value) {
        if ($value[$treeKey['pid']] != 0) {
            unset($value['redirect']);
            foreach ($arr as $k => &$v) {
                if ($v[$treeKey['id']] == $value[$treeKey['pid']]) {
                    if (isset($v['children'])) {
                        array_push($v['children'], $value);
                    } else {
                        $v['children'] = [];
                        array_push($v['children'], $value);
                    }
                }
            }
            unset($value);
        }
    }
    return $arr;
}

// 获取菜单树
function getLoginTree($data = [])
{
    if (empty($data)) {
        return [];
    }
    $arr = [];
    foreach ($data as $key => &$value) {
        $value['value'] = $value['id'];
        $value['label'] = $value['title'];
        if ($value['pid'] == 0 && $value['component'] && $value['component'] != '') {
            array_push($arr, $value);
            unset($value);
        }
    }

    foreach ($data as $key => &$value) {
        if ($value['pid'] != 0 && $value['component'] && $value['component'] != '') {
            unset($value['redirect']);
            foreach ($arr as $k => &$v) {
                if ($v['id'] == $value['pid']) {
                    if (isset($v['children'])) {
                        array_push($v['children'], $value);
                    } else {
                        $v['children'] = [];
                        array_push($v['children'], $value);
                    }
                }
            }
            unset($value);
        }
    }
    return $arr;
}

function getRules($data = [])
{
    $arr = [];
    foreach ($data as $key => $value) {
        if ($value['op']) {
            array_push($arr, $value['op']);
        }
    }
    return $arr;
}

function checkFields($fields, $data)
{
    if (is_array($fields)) {
        $keys = array_keys(array_intersect_key($fields, $data));
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $data[$key];
        }
        return $result;
    }
    return [];
}
