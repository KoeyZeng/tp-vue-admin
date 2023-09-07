<?php
namespace app\common\controller;

use app\BaseController;
use think\App;
use think\facade\Cache;
use think\facade\View;
use app\model\Seo as SeoModel;

class Layout extends BaseController
{
    /**
     * 导航栏
     * @var
     */
    protected $nav = null;
    /**
     * 模板
     * @var
     */
    protected $view = null;
    /**
     * 模板路径
     * @var
     */
    protected $viewPath = 'web@';

    /**
     * 模板
     * @var
     */
    protected $tdkArr = null;
    /**
     * TDK
     * @var
     */
    protected $tdk = [];
    /**
     * 路由
     * @var
     */
    protected $route = '';
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     *
     */
    public function initialize()
    {
        // 获取当前路由
        $this->route = strtolower(request()->pathinfo());
        if (strpos($this->route,'_') !== false) $this->route = substr($this->route,0,strpos($this->route,'_'));
        if (strpos($this->route,'/') !== false)  $this->route = substr($this->route,0,strpos($this->route,'/'));

        // 视图模板
        $this->view =  new View();

        if (is_mobile()) $this->viewPath = 'mobile@';

        // 获取缓存的TDK
        $this->tdkArr = Cache::get('tdk');
        if (empty($this->tdkArr)) {
            // 模板变量赋值
            $this->tdkArr = SeoModel::order('id desc')->column('id,title,keywords,description','full_url');
            Cache::set('tdk', $this->tdkArr);
        }

        $tdkKey = "/{$this->route}";
        $this->tdk = isset($this->tdkArr[$tdkKey]) ? $this->tdkArr[$tdkKey] : $this->tdkArr['/'];

        // 导航栏
        $this->nav = config('index.nav');
        $this->view::assign('route', $this->route);
        $this->view::assign('tdk', $this->tdk);
        $this->view::assign('alt', $this->tdkArr['/']['title']);
        $this->view::assign('nav', $this->nav);
    }

    /**
     * 重新构建TDK
     * @access public
     * @param  App  $app  应用对象
     *
     */
    public function setTDK($find, $type = 'news')
    {
        if (!empty($find['title'])) $this->tdk['title'] = $find['title'].'-'. $this->tdk['title'];
        if (!empty($find['keywords'])) $this->tdk['keywords'] = $find['keywords'];
        if (!empty($find['des'])) $this->tdk['description'] = $find['des'];
        if (!empty($find['page'])) $this->tdk['title'] = $this->tdk['title']."-第{$find['page']}页";
        $this->view::assign('tdk', $this->tdk);
    }


    /**
     * 生成指定个数，以及最小最大值随机数组(包括最大值)
     * @parem $min 随机数组最小值
     * @parem $max 随机数组最大值
     * @parem $num 随机数组个数,默认max-min
     * @parem $order 排序方式，false不排序，ture默认 由低到高-->asort()
     *
     * */
    public function uniqueRand($min, $max, $num = 10, $order = false)
    {
        // 转为 int 类型
        $min = gettype($min) == 'int' ? $min : intval($min);
        $max = gettype($max) == 'int' ? $max : intval($max);
        // 如果参数写反
        if ($max <= $min) {
            $max = $max + $min;
            $min = $max - $min;
        }

        $num = gettype($num) == 'int' ? $num : intval($num);
        $max_num = $max - $min; // 最大数组个数
        if ($num < 1 || $num > $max_num)  //随机数组个数,默认max-min
        {
            $num = $max_num;
        }

        //生成随机数组
        $return = array();
        $i = 0;
        while (count($return) < $num) {
            $i++;
            $rand_n = rand($min, $max);
            $return[$rand_n] = $i;
        }
        $return = array_flip($return);

        // 数组排序
        if (empty($order)) {
            $order = strtolower($order);
            switch ($order) {
                case 'asort':   //由低到高 ,键值关联的保持
                case 'arsort':  //由高到低 ,键值关联的保持
                case 'sort':    //由低到高
                case 'rsort':   //由高到低
                    $order($return);
                    break;
                default:
                    sort($return); //由低到高
                    break;
            }
        }
        return $return;
    }

    /**
     * 递归实现无限极分类
     * @param $array 分类数据
     * @param $pid 父ID
     * @return $tree 树结构数据
     */
    public function generateTree($array = [], $pid = 0)
    {
        $tree = array();
        foreach ($array as $key => $value) {
            if ($value['pid'] == $pid) {
                $value['children'] = $this->generateTree($array, $value['id']);
                if (!$value['children']) unset($value['children']);
                $tree[] = $value;
            }
        }
        return $tree;
    }

    /**
     * 获取当前页码
     * @return $tree 树结构数据
     */
    public function getPage()
    {
        $page = 0;
        if (preg_match('/page-(.*).html/', request()->pathinfo(), $matches)) {
            $page = $matches[1];
        }
        return $page;
    }
}
