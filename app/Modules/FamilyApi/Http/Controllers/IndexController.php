<?php
namespace App\Modules\FamilyApi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FamilyApi\Models\Blocks;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class IndexController extends Controller
{
    public function index()
    {
        return $this->returnJson('dd', '', Blocks::first());
    }

    public function encrypt()
    {
        return app('md5')->make('china', ['rounds' => 'aaaa']);
    }

    protected function toImg($original, $cover = '', $thumb = '')
    {
        $img              = pathinfo($original);
        $arr['cover_img'] = '/uploads/' . $img['dirname'] . '/' . $img['basename'];
        if ($cover) {
            $arr['general_img'] = '/uploads/' . str_replace('image/', 'thumb/', $img['dirname']) . '/' . $img['filename'] . $cover . '.' . $img['extension'];
        }
        if ($thumb) {
            $arr['thumb_img'] = '/uploads/' . str_replace('image/', 'thumb/', $img['dirname']) . '/' . $img['filename'] . $thumb . '.' . $img['extension'];
        }
        return $arr;
    }

    // 首页区块
    public function block()
    {
        $list = Db::connection('mysql_online')->table('blocks')->get();
        foreach ($list->toArray() as $block) {
            $content = explode("\n", $block->content);
            array_walk($content, function ($value) use ($block) {
                $value = explode("\t", $value);
                $data  = [
                    'title'       => $value[0],
                    'link'        => $value[1],
                    'description' => $value[3],
                    'lang'        => $block->lang_key == 'en' ? '1' : '2',
                    'city_id'     => $block->cityid,
                    'status'      => $block->publish,
                ];
                $data += $this->toImg($value[2], '_700_467_1');
                if ($block->createtime) {
                    $data['create_time'] = date('Y-m-d H:i:s', $block->createtime);
                }
                Db::connection('mysql_local')->table('blocks')->insert($data);
            });
        }
        return $list;
    }

    // 文章分类
    public function articleCat()
    {
        $list = Db::connection('mysql_online')->table('family_category')->get();
        foreach ($list->toArray() as $cat) {
            $data = [
                'cat_name'   => $cat->catename,
                'cat_id'     => $cat->id,
                'parent_id'  => $cat->pid,
                'channel_id' => $cat->channel_id,
                'alias'      => $cat->alias,
                'lang'       => $cat->lang_key == 'en' ? '1' : '2',
                'sort_order' => 50 + $cat->sortby,
            ];
            Db::connection('mysql_local')->table('article_cat')->insert($data);
        }
    }

    // 文章基本信息
    public function article()
    {
        $list = Db::connection('mysql_online')->table('family_arcindex')->orderBy('id', 'asc')->get();
        foreach ($list->toArray() as $art) {
            $data = [
                'art_id'     => $art->id,
                'channel_id' => $art->channel_id,
                'cat_id'     => $art->cid,
                'title'      => $art->subject,
                'subtitle'   => $art->subtitle,
                'author'     => $art->author,
            ];
            Db::connection('mysql_local')->table('article')->insert($data);
        }
    }

    // 文章内容
    public function contents()
    {
    	// 内容有问题
        $list = Db::connection('mysql_online')->table('contents')->get();
        foreach ($list->toArray() as $row) {
            $data = [
                'content' => $row->content,
            ];
            Db::connection('mysql_local')->table('article')->where('art_id', '=', $row->aid)->update($data);
        }
    }

    // 文章扩展
    public function online()
    {
        $list = Db::connection('mysql_online')->table('common_arcitem')->get();
        foreach ($list->toArray() as $row) {
            $data = [
                'status'      => $row->publish,
                'views'       => $row->views,
                'create_time' => date('Y-m-d H:i:s', $row->createtime),
                'update_time' => date('Y-m-d H:i:s', $row->updatetime),
                'attrib'      => $row->attrib,
                'delete_flag' => $row->delflag,
                'lang'        => $row->lang_key == 'en' ? '1' : '2',
            ];
            if ($row->coverpic) {
            	$data += $this->toImg($row->coverpic, '_700_460_1', '100_67_1');
            }
            if ($row->alias) {
                $data['alias'] = $row->alias;
            }
            Db::connection('mysql_local')->table('article')->where('art_id', '=', $row->aid)->update($data);
        }
    }
}
