<?php
/**
 * User: weiqing
 * Date: 2021/11/29
 */

namespace WeiQing\Library\Extend;

class DataExtend
{
    /**
     * 一维数组生成数据树
     * @param array $list 待处理数据
     * @param string $cid 自己的主键
     * @param string $pid 上级的主键
     * @param string $sub 子数组名称
     * @return array
     */
    public static function arr2tree(array $list, string $cid = 'id', string $pid = 'pid', string $sub = 'sub'): array
    {
        [$tree, $temp] = [[], array_combine(array_column($list, $cid), array_values($list))];
        foreach ($list as $vo) if (isset($vo[$pid]) && isset($temp[$vo[$pid]])) {
            $temp[$vo[$pid]][$sub][] = &$temp[$vo[$cid]];
        } else {
            $tree[] = &$temp[$vo[$cid]];
        }
        return $tree;
    }

    /**
     * 一维数组生成数据树
     * @param array $list 待处理数据
     * @param string $cid 自己的主键
     * @param string $pid 上级的主键
     * @param string $cpath 当前 PATH
     * @param string $ppath 上级 PATH
     * @return array
     */
    public static function arr2table(array $list, string $cid = 'id', string $pid = 'pid', string $cpath = 'path', string $ppath = ''): array
    {
        $tree = [];
        foreach (static::arr2tree($list, $cid, $pid) as $attr) {
            $attr[$cpath] = "{$ppath}-{$attr[$cid]}";
            $attr['sub'] = $attr['sub'] ?? [];
            $attr['spc'] = count($attr['sub']);
            $attr['spt'] = substr_count($ppath, '-');
            $attr['spl'] = str_repeat("　├　", $attr['spt']);
            $sub = $attr['sub'];
            unset($attr['sub']);
            $tree[] = $attr;
            if (!empty($sub)) {
                $tree = array_merge($tree, static::arr2table($sub, $cid, $pid, $cpath, $attr[$cpath]));
            }
        }
        return $tree;
    }

    /**
     * @param $arr
     * @param int $pid
     * @param int $depth
     * @param string $p_sub
     * @param string $c_sub
     * @param string $d_sub
     * @return array
     */
    public static function handleTreeList($arr, int $pid = 0, int $depth = 0, string $p_sub='parent_id', string $c_sub='children', string $d_sub='depth'): array
    {
        $returnArray = [];
        if(is_array($arr) && $arr) {
            foreach($arr as $k => $v) {
                if($v[$p_sub] == $pid) {
                    $v[$d_sub] = $depth;
                    $tempInfo = $v;
                    unset($arr[$k]); // 减少数组长度，提高递归的效率，否则数组很大时肯定会变慢
                    $temp = self::handleTreeList($arr,$v['id'],$depth+1,$p_sub,$c_sub,$d_sub);
                    if ($temp) {
                        $tempInfo[$c_sub] = $temp;
                    }
                    $returnArray[] = $tempInfo;
                }
            }
        }
        return $returnArray;
    }

    /**
     * 获取数据树子ID集合
     * @param array $list 数据列表
     * @param mixed $value 起始有效ID值
     * @param string $ckey 当前主键ID名称
     * @param string $pkey 上级主键ID名称
     * @return array
     */
    public static function getArrSubIds(array $list, $value = 0, string $ckey = 'id', string $pkey = 'pid'): array
    {
        $ids = [intval($value)];
        foreach ($list as $vo) if (intval($vo[$pkey]) > 0 && intval($vo[$pkey]) === intval($value)) {
            $ids = array_merge($ids, static::getArrSubIds($list, intval($vo[$ckey]), $ckey, $pkey));
        }
        return $ids;
    }
}