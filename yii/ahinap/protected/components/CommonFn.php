<?php
/**
 * Created on 2012-6-20
 * add by wangyang
 * 全局公共函数
 */
class CommonFn
{
    /**
     *  获取easyui datagrid分页参数 返回 limit, offset, order 如果no_page=1 不分页
     */
    public static function getPageParams(){
        $page = Yii::app()->request->getParam('page');
        $rows = Yii::app()->request->getParam('rows');
        $sort = Yii::app()->request->getParam('sort');
        $order = Yii::app()->request->getParam('order');
        $no_page = 0;
        if ($page == null){
            $page = 1;
            $no_page = 1;
        }
        if ($rows == null){
            $rows = 20;
        }
        $new_sort = array();
        if ($sort){
            $sorts = explode(',', $sort);
            $orders = explode(',', $order);     
            for ($i = 0;$i < count($sorts);$i ++){
                if ($orders[$i] == 'asc'){
                    $temp = EMongoCriteria::SORT_ASC;       
                } else {
                    $temp = EMongoCriteria::SORT_DESC;  
                }
                $new_sort[$sorts[$i]] = $temp;
            }
        }
        $offset = ($page - 1) * $rows;
        $result = array('offset' => $offset, 'sort' => $new_sort);
        if ($no_page == 0){
            $result['limit'] = $rows;
        }   
        return $result;
    }

    //提升效率,简化数据读取操作
    public static function getRows($e_cursor){
        $rows = array();
        $e_cursor->next();  
        while($row = $e_cursor->current()){
            $t = $row->attributes;
            $rows[] = $t;
            $e_cursor->next();
        }
        return $rows;
    }
    
    /**
     * 从cursor游标得到数组
     * 同时获取该记录的操作者
     */
    public static function getRowsFromCursor($e_cursor){
        $rows = array();
        $e_cursor->next();  
        $_ids = array();
        $user_ids = array();
        while($row = $e_cursor->current()){
            $t = $row->attributes;
            $rows[] = $t;
            $_ids[] = $t['_id'];
            if (isset($t['user'])){
                if (!is_numeric($t['user'])){ //不是管理员用户
                    $user_ids[] = $t['user'];
                }
            }
            $e_cursor->next();
        }
        $total = count($rows);
        if ($total > 0){
            $model = $e_cursor->getModel();
            $db_name = $model->getMongoDBComponent()->dbName;
            $c_name = $model->getCollectionName();      
            $criteria = new EMongoCriteria();
            $criteria->db_name('==', $db_name);
            $criteria->c_name('==', $c_name);
            $criteria->r_id('in', $_ids);
            $criteria->limit($total);
            $cursor = DbAction::model()->findAll($criteria);
            //var_dump($_ids);exit;
            if ($cursor->count() > 0){
                $action_info = array();
                $admin_user_ids = array();
                foreach ($cursor as $v){
                    $_id = (string)$v->r_id;
                    $action = $v->action;
                    $last = count($action) - 1;
                    $admin_user_ids[] = $action[$last]['user'];
                    $action_info[$_id] = array(
                        'action_time' => date("Y-m-d H:i", $action[$last]['time']), 
                        'admin_id' => $action[$last]['user'],
                        'action_log' => isset($action[$last]['action_log']) ? $action[$last]['action_log'] : ''
                    );
                }
                $criteria = new EMongoCriteria();
                $criteria->_id('in', $admin_user_ids);
                $user_cursor = User::model()->findAll($criteria);
                $ruser_cursor = RUser::model()->findAll($criteria);
                $admin_names = array();
                foreach ($user_cursor as $v){
                    $admin_names[$v->_id] = $v->name;
                }
                foreach ($ruser_cursor as  $v) {
                    $admin_names[(string)$v->_id] = $v->user_name;
                }
                foreach ($rows as $k => $v){
                    $_id = (string)$v['_id'];
                    if (isset($action_info[$_id])){
                        $admin_id = (string)$action_info[$_id]['admin_id'];
                        $admin_user = $admin_names[$admin_id];
                        $rows[$k]['action_user'] = $admin_user;
                        $rows[$k]['action_time'] = $action_info[$_id]['action_time'];
                        $rows[$k]['action_log'] = $action_info[$_id]['action_log'];
                    } else {
                        $rows[$k]['action_user'] = '';
                        $rows[$k]['action_time'] = '';
                        $rows[$k]['action_log'] = '';
                    }           
                }
            } else {
                foreach ($rows as $k => $v){
                    $rows[$k]['action_user'] = '';
                    $rows[$k]['action_time'] = '';
                    $rows[$k]['action_log'] = '';   
                }
            }
        }
        return $rows;
    }
    
    /**
     * 组合easyui datagrid json数据
     * 当参数里面不包含数量时
     */
    public static function composeDatagridData($rows, $total="", $more=""){
        $result = array();
        if ($total === ""){
            $result = $rows;
        } else {
            $result['rows'] = $rows;
            $result['total'] = $total;
            $result['more'] = $more;
            if (is_array($more) && isset($more['footer'])){
                $result['footer'] = $more['footer'];
            }
        }
        $debug = Yii::app()->request->getParam('debug');
        if ($debug !== null){
            $result['exec_time'] = Yii::getLogger()->getExecutionTime();
        }
        return json_encode($result);
    }

    /**
     *  根据$response 返回 json
     */
    public static function requestAjax($response=true, $message="", $data=array(),$error_code=200,$special_data = array())
    {   
        if($response){
            $res = array('success' => $response, 'message' => $message,'data' =>$data);
        //当错误码为203时，response为false,但依然需要返回数据
        }elseif($error_code==203){
            $res = array('success' => $response, 'message' => $message,'data' =>$data);
        }else{
            if(!empty($data)){
                $res = array('success' => $response, 'message' => $message,'data' =>$data);
            }else{
                $res = array('success' => $response, 'message' => $message); 
            }
        }
        if(!empty($special_data)&&is_array($special_data)){
            foreach ($special_data as $key => $value) {
                $res[$key] = $value;
            }
        }

        $debug = Yii::app()->request->getParam('debug');
		$callback = Yii::app()->getRequest()->getParam("callback");
        if($error_code && is_numeric($error_code)){
            $res['error_code'] = $error_code;
        }else{
            $res['error_code'] = 200;
        }
        if ($debug !== null){
            $res['exec_time'] = Yii::getLogger()->getExecutionTime();
        }
		header('Content-type: application/json');
		
		if ($callback && $callback != '') {
			echo $callback . '(' . json_encode($res) . ')';
		} else {
			echo json_encode($res);
		}
        
        exit();
    }
    
    /**
     * 将配置数组转为combobox数据列表
     * $config = array(value1 => 'text1', value2 => 'text2' ...) or array(value1 => array('name' => 'text1') ...);
     * $specified 指定的初始值
     * $all 是否添加全部选项
     * $all_value 全部选项的值
     */
    public static function getComboboxData($config, $specified='', $all=true, $all_value = ''){
        $data = array();
        if ($all){
            $temp = array('value' => $all_value, 'text' => '全部');
            if ($specified == $all_value){
                $temp['selected'] = true;
            }
            $data[] = $temp;
        }
        foreach ($config as $k => $v){
            if (is_array($v)){
                $name = $v['name'];
            } else {
                $name = $v;
            }
            $temp = array('value' => $k, 'text' => $name, 'attributes' => $v);
            if ($specified == $k){
                $temp['selected'] = true;
            }
            $data[] = $temp;
        }
        return $data;
    }
    
    /**
     *  将数据库取出的二维数组生成easyui combotree所需要的json数据
     *  params： $datas 数据库取出二维数组,  $key 每个item的唯一id, $value 每个item的描述, $level 用来区分组的数据列名
     *  注意: 改函数只针对特定表类型的二级目录
     */
    public static function composeCombotreeData($datas, $key, $value, $level){
        $type_list = array();
        $temp_array = array();
        //按组名重组数组
        foreach ($datas as $k => $v){
            $temp_array[$v[$level]][] = $v;
        }
        foreach ($temp_array as $k => $v){
            $temp_array1 = array();   //缓存一级目录
            $temp_array1['text'] = $k;
            $temp_array1['children'] = array();
            foreach ($v as $k1 => $v1){
                $temp_array2 = array();   //缓存二级目录
                $temp_array2['id'] = $v1[$key];
                $temp_array2['text'] = $v1[$value];
                array_push($temp_array1['children'], $temp_array2);
            }
            array_push($type_list, $temp_array1);
        }
        return $type_list;
    }
    
    public static function composeTreeData($rows, $key='_id', $value='name', $parent='parent', $level='level'){
        $tree_data = array();
        $level_array = array();
        //按组名重组数组
        $max_level = 0;
        foreach ($rows as $k => $v){
            $level_array[$v[$level]][] = $v;
            if ($max_level < $v[$level]){
                $max_level = $v[$level];
            }
        }
        $child_data = array();
        for ($i = $max_level;$i >= 1;$i --){
            $level_data = $level_array[$i];
            foreach ($level_data as $k => $v){
                $v[$key] = (string)$v[$key];
                $v[$parent] = (string)$v[$parent];
                $temp = array('id' => $v[$key], 'text' => (string)$v[$value], 'attributes' => $v);
                if (isset($child_data[$v[$key]])){
                    $temp['children'] = $child_data[$v[$key]];
                } else {
                    $temp['children'] = array();
                }       
                if ($i == 1){
                    $tree_data[] = $temp;
                } else {
                    $child_data[$v[$parent]][] = $temp;
                }
            }
        }
        return $tree_data;
    }
    
    public static function getLevelCode($code_len, $now_code=1){
        $now_code = (string)$now_code;
        $t_len = strlen($now_code);
        if ($t_len < $code_len){
            for ($i = 0;$i < ($code_len - $t_len);$i ++){
                $now_code = '0' . $now_code;
            }
        }
        return $now_code;
    }
    
    /**
     * 返回指定场景下的选项
     */
    public static function getScenarioOption($all_option, $scenario=''){
        $options = array();
        foreach ($all_option as $k => $v){
            if ($scenario == '' || (isset($v[$scenario]) && $v[$scenario])){
                $options[$k] = $v;
            }
        }
        return $options;
    } 
    
    /**
     *  除了超级管理员或者指定的角色只获取自己的数据
     *  用于需要进行过滤user_id的查询
     *  返回 array(
     *              'user_id' => $user_id,
     *              'filter' => 1 需要过滤 0 不需要过滤
     *          );
     */
    public static function filterByUserId($role='')
    {
        $user_id = Yii::app()->user->id;
        $filter = 1;
        $result = array();
        $result['user_id'] = $user_id;
        $result['filter'] = 0;
        if ($user_id == ''){ //未登录
            return $result;
        }
        if (Yii::app()->user->checkAccess(Helper::findModule('srbac')->superUser)){ //超级管理员
            $filter = 0;
        }
        if ($role != '' && Yii::app()->user->checkAccess($role)){ //指定的角色
            $filter = 0;
        }
        $result['filter'] = $filter;
        return $result;
    }

    /**
     *  将unicode转化为utf-8编码
     */
    public static function unescape($str)
    {
        $str = rawurldecode($str);
        preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U",$str,$r);
        $ar = $r[0];
        //print_r($ar);
        foreach($ar as $k=>$v) {
            if(substr($v,0,2) == "%u")
                $ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,-4)));
            elseif(substr($v,0,3) == "&#x")
                $ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,3,-1)));
            elseif(substr($v,0,2) == "&#") {
                //echo substr($v,2,-1)."\n";
                $ar[$k] = iconv("UCS-2","UTF-8",pack("n",substr($v,2,-1)));
            }
        }
        return join("",$ar);
    }

    public static function get_val_if_isset($var, $key,  $defaul_val=''){
        return (isset($var) && isset($var[$key]) && $var[$key]!==null)?  $var[$key] : $defaul_val;
    }

    public static function parse_break($str){
        return str_replace("\r\n", "\n", $str);
    }

    public static function parse_break_web($str){
        return preg_replace("/\r\n|\n/","<br />",$str);
    }


    public static  $empty = array();




    /**
     * 简单获取远程文件数据
     *
     * curl方式获取远程文件信息
     * @param string $url 要获取的网址
     * @return string 获取的链接内容
     */
    public static function simple_http($url) {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $res = curl_exec($ch);
          curl_close($ch);
          return $res;
    }

    /*去除url requset部分*/
    public static function clearUrl($url){
        $rstr='';
        $tmparr=parse_url($url);
        $rstr=empty($tmparr['scheme'])?'http://':$tmparr['scheme'].'://';
        $rstr.=$tmparr['host'].$tmparr['path'];
        return $rstr;
    }
    
    /**
     * 将数组总的MongoId转为字符串
     */
    public static function formatDataForJS($row){
        foreach ($row as $k => $v){
            if (is_a($v, 'MongoId')){
                $row[$k] = (string)$v;
            }
            if ($v === null){
                $row[$k] = '';
            }
        }
        return $row;
    }
    
    /**
     * 是否是mongoid
     * 当前驱动不支持判断mongoid，先自定义
     */
    public static function isMongoId($char){
        if (method_exists(new MongoId(), 'isValid')){
            return MongoId::isValid($char);
        } else {
            return !preg_match('/[\x80-\xff]./', $char) && strlen($char) == 24;
        }
    }


    //检查图标和图片的格式
    public static function checkPicFormat($pic){
        $pattern = "/^http:\/\/.*?\/.*?\.(jpg|png|gif|jpeg)/i";

        if (!preg_match( $pattern, strtolower($pic))){
            return false;
        }else{
            return true;
        }
    }

    //检查音频的格式
    public static function checkVoiceFormat($voice){
        $pattern = "/^http:\/\/.*?(.qiniudn.com)\/.*?/i";

        if (!preg_match( $pattern, strtolower($voice))){
            return false;
        }else{
            return true;
        }
    }

    //检查视频的格式
    public static function checkVideoFormat($video){
        $pattern = "/^http:\/\/.*?(.qiniudn.com)\/.*?/i";

        if (!preg_match( $pattern, strtolower($video))){
            return false;
        }else{
            return true;
        }
    }

    //时间格式化
    public static function  sgmdate($dateformat, $timestamp='', $format=0) {
        if(empty($timestamp)) {
            $timestamp = time();
        }
        $timeoffset = 8;
        $result = '';
        if($format) {
            $time = time() - $timestamp;
            if($time > 12*30*24*3600) {
                $result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
                //$result = intval($time/31104000).'年前';
            }elseif($time > 30*24*3600) {
                $result = intval($time/2592000).'个月前';
            }elseif($time > 24*3600) {
                $result = intval($time/86400).'天前';
            } elseif ($time > 3600) {
                $result = intval($time/3600).'小时前';
            } elseif ($time > 60) {
                $result = intval($time/60).'分钟前';
            } elseif ($time > 0) {
                $result = $time.'秒前';
            } else {
                $result = '刚刚';
            }
        } else {
            $result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
        }
        return $result;
    }

    //时间格式化   格式化未来的时间    如1天之后
    public static function bgmdate($dateformat, $timestamp='', $format=0){
        if(empty($timestamp)) {
            $timestamp = time();
        }
        $timeoffset = 8;
        $result = '';
        if($format) {
            $time = $timestamp-time();

            if($time > 24*3600) {
                $result = intval($time/86400).'天后';
            } elseif ($time > 3600) {
                $result = intval($time/3600).'小时后';
            } elseif ($time > 60) {
                $result = intval($time/60).'分钟后';
            } elseif ($time > 0) {
                $result = $time.'秒后';
            }elseif ($time < 0) {
                $result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
            } else {
                $result = '马上';
            }
        } else {
            $result = gmdate($dateformat, $timestamp + $timeoffset * 3600);
        }
        return $result;
    }

    public static function formatTimestamp($timestamp,$format="Y-n-d"){
        $timeoffset = 8;    //时区
        return gmdate($format, $timestamp + $timeoffset * 3600);
    }
    
    /**
     *returnInfo 格式化返回信息
     *@author leeon <leeon_on@qq.com>
     *@param bool $status 执行状态
     *@param string $info 提示信息
     *@param array $data 需要时返回数据
     */
    public static function returnInfo($status = true,$info = '',$data = array(),$special_data = array()){
        return array('status'=>$status,'info'=>$info,'data'=>$data,'special_data' => $special_data);
    }

    /**
     * getFirstTime 获得客户端首次请求时间戳
     * @param string $actiontype 推荐事先定义在ActionTimeRedis,使用ActionTimeRedis::TYPE_GET_GROUP方式调用
     * @param string $device_id 机器码
     *@param int $page 分页页码
     *@param string $order 排序方式
     */
    public static function getFirstTime($actiontype,$device_id,$page,$order='time'){
        $atimeAr = new ActionTimeRedis();
        if($page==1){
            $key = HelperKey::generateActionTimeKey($actiontype,$device_id,1,$order);
            $redisdata = array('actiontime'=>time());
            $atimeAr->set($key,$redisdata);
            $res = $atimeAr->get($key);
        }else{
            $key = HelperKey::generateActionTimeKey($actiontype,$device_id,1,$order);
            $res = $atimeAr->get($key);
            if(empty($res)){
                $redisdata = array('actiontime'=>time());
                $atimeAr->set($key,$redisdata);
                $res = $atimeAr->get($key);
            }
        }
        return intval($res['actiontime']);
    }

    /**
     * getPagedata 获取分页数据
     * @author leeon <leeon_on@qq.com>
     * @param string $model 要获取数据的model
     * @param string $page 具体第几页
     * @param int $pagesize 分页大小
     * @param string $conditions 查询条件
     * 使用示例:$conditions = array(
     *                                'group'=>array('==',$group_id),
     *                                'status'=>array('==',1),
     *                                'time'=>array('<=',$actiontime)
     *                           );
     *@param string $order 排序方式
     * 使用示例: $order = array(
     *                         'time'=>'desc',
     *                        );
     *@return array
     */
    public static function getPagedata($model,$page,$pagesize,$conditions = array(),$order = array(),$need_parse = true,$sum_page = true){
        $customer_pagesize = intval(Yii::app()->getRequest()->getParam("page_size",0));
        if($customer_pagesize>0){
            $pagesize = $customer_pagesize;
        }
        $criteria = new EMongoCriteria();
        foreach ($conditions as $key => $value) {
            $criteria->$key($value[0],$value[1]);
        }
        if($sum_page){
            $sum_count = $model->count($criteria);
            $sum_page = ceil($sum_count/$pagesize);
        }else{
            $sum_count = 9999;
            $sum_page = 99;
        }
        
        if($page<1){
            $res = array();
            $result['sum_count'] = $sum_count;
            $result['sum_page'] = $sum_page;
            $result['current_page'] = $page;
            $result['page_size'] = $pagesize;   
            $result['res'] = $res; 
            return $result;
        }
        if($order){
            foreach ($order as $key => $value) {
                $orderparam = 'EMongoCriteria::SORT_'.strtoupper($value);
                $criteria->sort($key,constant($orderparam));
            }
        }
        $criteria->limit($pagesize);
        $offset = $page*$pagesize-$pagesize;
        $criteria->offset($offset);
        $cursor = $model->findAll($criteria);
        $rows = self::getRows($cursor);
        if($need_parse){
            $res = $model->parse($rows);
        }else{
            $res = $rows;
        }
        $result['sum_count'] = $sum_count;
        $result['sum_page'] = $sum_page>1?$sum_page:1;
        $result['current_page'] = $page;
        $result['page_size'] = $pagesize;
        $result['res'] = $res; 
        return $result;
    }


    /**
     * 判断对象在model内是否已存在,存在时返回此对象，为api模块控制器封装,不建议在ApiBaseControllers外的地方使用
     * @author leeon <leeon_on@qq.com>
     *@param string $id 要查询对象id
     *@param string $Zmodel 要查询的model的Z组件如:ZTopic
     *@param string $errorinfo 当对象不存在时向客户端返回的错误信息
     *@return  object 如存在则返回此对象对象
     */
    public static function apigetObJ($id,$model,$errorinfo = '此id不存在',$errorcode = ''){
        if(CommonFn::isMongoId($id)){
            $_id = new MongoId($id);
            $obj =  new $model();
            $_obj = $obj->get($_id);
            if($_obj&&isset($_obj->attributes)&&!empty($_obj->attributes)){
                return $_obj;
            }else{
                if ($errorcode) {
                    CommonFn::requestAjax(false,$errorinfo,array(),$errorcode);
                }else{
                    CommonFn::requestAjax(false,$errorinfo);
                }
            }
        }else{
            if ($errorcode) {
                CommonFn::requestAjax(false,$errorinfo,array(),$errorcode);
            }else{
                CommonFn::requestAjax(false,$errorinfo);
            }
        }   
    }

    /**
     * 获取对象
     *@param string $id 要查询的id
     *@param string $Zmodel 要查询的model的Z组件如:ZTopic
     *@return  object model存在的这个对象
     */
    public static function getObj($id,$Zmodel){
        if(CommonFn::isMongoId($id)){
            $_id = new MongoId($id);
            $obj =  new $Zmodel();
            $_obj = $obj->get($_id);
            if($_obj&&isset($_obj->attributes)&&!empty($_obj->attributes)){
                return $_obj;
            }else{
                return false;
            }
        }else{
            return false;
        }   
    }

    /**
     * 内容合法性检测
     * @author leeon <leeon_on@qq.com>
     *@param array $data 内容数组，包含需要检测的数据
     * 示例:$data = array(
     *                                'content'=>'呵呵呵呵',
     *                                'pics'=>array(‘http:xxxxxx.jpg’,'jkkkk.jpg'),
     *                           );
     *@param string $type 按何类型检测  
     */
    public static function checkContent($data,$type){
        // if($type=='topic' || $type == 'post'){
        if($type=='topic'){
            $data['content'] = preg_replace(array('/\[.*?\]/','/(沙发|顶)/'),'',$data['content']);
            $data['content'] = urlencode($data['content']);
            $data['content'] = preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99)+/",'',$data['content']);
            $data['content'] = urldecode($data['content']);
        }
        
        if($type=='topic'){
            if(empty($data['pics']) && empty($data['video'])){
                if(mb_strlen($data['content'],'utf-8')>4000){
                    return false;
                }elseif(mb_strlen($data['content'],'utf-8')<4 && mb_strlen($data['content'],'utf-8')>=0){
                    return false;
                }
            }
            //判断图片是否合法
            if(!empty($data['pics'])){
                foreach ($data['pics'] as $value) {
                    if(!is_array($value)||empty($value['url'])||empty($value['width'])||empty($value['height'])){
                        return false;
                    }
                }
            }
            //判断视频是否合法
            if(!empty($data['video'])){
                if(!is_array($data['video'])||empty($data['video']['url'])||empty($data['video']['length'])){
                    return false;
                }
            }
            return true;
        }elseif ($type=='post') {
            $status = 0;
            foreach ($data as  $value) {
                if(!empty($value)){
                    $status+=1;
                }
            }
            if($status<1){
                return false;
            }
            // if(!empty($data['content'])){
            //     if(mb_strlen($data['content'],'utf-8')>2000||mb_strlen($data['content'],'utf-8')<1){
            //         return false;
            //     }
            // }
            //判断图片是否合法
            if(!empty($data['pics'])){
                foreach ($data['pics'] as $value) {
                    if(!is_array($value)||empty($value['url'])||empty($value['width'])||empty($value['height'])){
                        return false;
                    }
                }
            }
            //判断视频是否合法
            if(!empty($data['video'])){
                if(!is_array($data['video'])||empty($data['video']['url'])||empty($data['video']['length'])||empty($data['video']['avatar'])){
                    return false;
                }
            }
            //判断语音是否合法
            if(!empty($data['voice'])){
                if(!is_array($data['voice'])||empty($data['voice']['url'])||empty($data['voice']['length'])){
                    return false;
                }
            }
            return true;
        }elseif($type=='message'){
            $status = 0;
            foreach ($data as  $value) {
                if(!empty($value)){
                    $status+=1;
                }
            }
            if($status<1){
                return false;
            }
            if(!empty($data['content'])){
                if(mb_strlen($data['content'],'utf-8')>2000||mb_strlen($data['content'],'utf-8')<1){
                    return false;
                }
            }
            //判断图片是否合法
            if(!empty($data['pics'])){
                foreach ($data['pics'] as $value) {
                    if(!is_array($value)||empty($value['url'])||empty($value['width'])||empty($value['height'])){
                        return false;
                    }
                }
            }
            //判断视频是否合法
            if(!empty($data['video'])){
                if(!is_array($data['video'])||empty($data['video']['url'])||empty($data['video']['length'])||empty($data['video']['avatar'])){
                    return false;
                }
            }
            //判断语音是否合法
            if(!empty($data['voice'])){
                if(!is_array($data['voice'])||empty($data['voice']['url'])||empty($data['voice']['length'])){
                    return false;
                }
            }
            return true;
        }else{
            return false;
        }
    }

    /**
     * 返回系统消息提示信息
     * 按照不同的模块分别读取预定义数据
     * @param string $type
     * @param $key
     * @author guoqiang.zhang
     * @date 2014-10-27
     */
    public static function getMessage($type = 'user',$key){
        /**
         * 消息类型分为coreMessage和message
         * type='zii' 和 'yii' 为coreMessage ，对应的message文件为protected/messages/{LOCALID}/yii.php
         * type 对应其他值时。对应message文件为protected/messages/{LOCALID}/$type.php
         * @link http://www.yiiframework.com/doc/guide/1.1/zh_cn/topics.i18n
         */
        return Yii::t($type,$key);
    }

     /**
     * 通过curl方式获取制定的图片到本地 
     * @param string $url 完整的图片地址  
     * @param string $filename 要存储的文件名   
     */
    public static function getImageByUrl($url,$filename){
        if(is_dir(basename($filename))) { 
            return false; 
        } 
        //去除URL连接上面可能的引号 
        $url = preg_replace( '/(?:^[\'"]+|[\'"\/]+$)/','',$url); 
        $hander = curl_init(); 
        $fp = fopen($filename,'wb'); 
        curl_setopt($hander,CURLOPT_URL,$url); 
        curl_setopt($hander,CURLOPT_FILE,$fp); 
        curl_setopt($hander,CURLOPT_HEADER,0); 
        curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1); 
        //curl_setopt($hander,CURLOPT_RETURNTRANSFER,false);//以数据流的方式返回数据,当为false是直接显示出来 
        curl_setopt($hander,CURLOPT_TIMEOUT,60); 
        curl_exec($hander); 
        curl_close($hander); 
        fclose($fp); 
        return true; 
    }

    /**
     * 上传文件到七牛
     * @param string $file 源文件  
     * @param string $upname 上传后的文件名  
     * @param string $bucket 七牛上传的位置 
     */
    public static function upFiletoQiniu($file,$upname,$bucket){
        $path = Yii::getPathOfAlias('application');
        require_once($path."/vendors/qiniu/rs.php");
        require_once($path."/vendors/qiniu/io.php");
        $qiniu_config = Yii::app()->params['qiniuConfig'];
        $accessKey = $qiniu_config['ak'];
        $secretKey = $qiniu_config['sk'];
        Qiniu_SetKeys($accessKey, $secretKey);
        $putPolicy = new Qiniu_RS_PutPolicy($bucket);
        $upToken = $putPolicy->Token(null);
        $putExtra = new Qiniu_PutExtra();
        $putExtra->Crc32 = 1;
        list($ret,$err) = Qiniu_PutFile($upToken,$upname,$file,$putExtra);
        if ($err !== null) {
            return false;
        } else {
            return true;
        }
    }

    //根据总记录数和每页显示数来生成最大页，并检查请求的页码是否合法
    public static function getMaxPage($total,$items_per_page){
        $max_pages = ceil($total / $items_per_page );
        if($max_pages==0){
            $max_pages=1;
        }
        return $max_pages;
    }

    //记录错误日志
    public static function addErrorLog($type,$info){
        $model = new ZErrorRcord();
        $model->setErrorRecord($type,$info);
    }


    /**
     * 根据系统环境配置变量来获取对应的后台发送私信的客服id
     * 环境变量设定参照/index.php
     * @return bool
     */
    public static function getMsgAdminID(){
        return Yii::app()->params['kefu_user'];
    }

    //float型的数字比较     比如版本的比较
    public static function isBigger($now, $latest)
    {
        //纯数字比较
        if (is_numeric($now) && is_numeric($latest)) {
            if ($latest > $now) {
                return true;
            } else {
                return false;
            }
        }
        //x.x.x.x比较
        if (stripos($latest, '.') !== false) {
            $f = explode('.', $now);
            $s = explode('.', $latest);
            $count = count($f);
            foreach ($f as $k => $v) {
                //比如1.0比2.0
                if ($s[$k] > $v) {
                    return true;
                }
                //前面几位相等没关系，最后一位必须大于
                //比如1.0.5比1.0.8
                if (($count == $k + 1) && ($s[$k] > $v)) {
                    return true;
                }
            }
        }
        return false;
    }

    //新增
    public static function inc($db, $collection, $_id, $field, $key=null,  $inc=1){
        $mongo = new MongoClient(DB_CONNETC);

        if($key){
            $field2=$field.'.'.$key;
        }else{
            $field2=$field;
        }
        $res=$mongo->$db->command(array("findandmodify" => $collection, "query" => array('_id'=>$_id), 'update'=>array('$inc'=>array($field2=>$inc)), 'fields'=>array('_id'=>1, $field2=>1),  'upsert'=>true));

        if(!$res || !isset($res['value']) || !isset($res['value'][$field]) || ($key && !isset($res['value'][$field][$key]) )){
            return 1;
        }

        if($key) return $res['value'][$field][$key]+1;

        return $res['value'][$field]+1;

    }

    //为了防止用户的滥用行为，对用户特定动作进行记录，某一天不能超多特定次数
    //目前记录的行为有：
    //1. send_topic 2.send_post 3.send_message
    //
    public static function record_user_action($user_id, $act){
        $today = intval(date("Ymd"));
        $_id=$today.'-'.$act.'-'.$user_id;
        return inc('wozhua_data', 'user_records', $_id, 'times');
    }

    //记录设备的操作记录
    public static function record_device_action($act, $ip)
    {
        $_id=$today = intval(date("Ymd")).'-'.$act.'-'.$ip;
        return inc('wozhua_data', 'user_records', $_id, 'times');
    }

    //返回设备的操作记录
    public static function get_ip_action($act, $ip)
    {
        $mongo = new MongoClient(DB_CONNETC);
        $_id=intval(date("Ymd")).'-'.$act.'-'.$ip;
        $row=$mongo->wozhua_data->user_records->findOne(array('_id'=>$_id));
        return $row && $row['times'] ? $row['times'] : 0;
    }

    //获得用户自上次访问后的天数
    public static function get_user_last_visit_days($last_vt){
        $all_days=array(1, 3, 7, 30);

        $days=ceil((strtotime(intval(date("Ymd")))-$last_vt)/86400);

        foreach($all_days as $day){
            if($days<=$day){
                return $day;
            }
        }
        return 30;
    }

    //去掉描述末尾的符号，加上省略号
    public static function add_more_to_str($str){
        return preg_replace("/(。|？|！|~|,|，|…|\!|\.|\?|;|；|～|~|、){1,6}$/", "", $str)."...";
    }

    //判断是否是微信浏览器
    public static function is_weixin(){
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')!==false){
          return true;
        }
        return false;
    }
    //异步触发任务
    public static function backend($url){
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        //执行命令
        $res = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
    }

    //把某些url 生成图片截图
    public static function screenshot($type,$id,$title,$width)
    {
        $file=APP_PATH."/images/temp/".md5($type."_".$id."v2").".jpg";

        if(!file_exists($file)){
            if(ENVIRONMENT=='develop' || ENVIRONMENT=='test'){
                $q=" -q";
                $host = "http://wwwtest.wozhua.mobi/";
            }else{
                $q="";
                $host = "http://www.wozhua.mobi/";
            }
            $url = $host.$type.'/'.$id."&screenshot=1";
            $cmd="wkhtmltoimage --width $width $q --quality 90 \"{$url}\" $file";

            exec($cmd);
        }

        $content=file_get_contents($file);
        header('Content-type:image/jpeg');
        header('Content-Length:'.strlen($content));

        echo $content;
        exit;
    }

    /**
     * 七牛音频转码 amr2MP3
     * @param string $bucket 七牛上传的位置 
     * @param string $upname 源文件key 
     * 生成的MP3保存在同个bucket下，与原文件同名，后缀为MP3
     */
    public static function ConvVoice($bucket,$key){
        if(!strpos($key,'amr')){
            return false;
        }
        $new_name = str_replace('amr','mp3',$key);
        $entry = $bucket.':'.$new_name;
        $encodedEntryURI = self::urlsafe_base64_encode($entry);
        $op = 'avthumb/mp3/ar/44100/aq/3|saveas/'.$encodedEntryURI;
        $qiniu_config = Yii::app()->params['qiniuConfig'];
        $accessKey = $qiniu_config['ak'];
        $secretKey = $qiniu_config['sk'];
        $url = 'http://api.qiniu.com';
        $query = 'bucket='.urlencode($bucket).'&key='.urlencode($key).'&fops='.urlencode($op);
        $variety_str = "/pfop/\n".$query;
        $access_token = self::generate_token($accessKey,$secretKey,$variety_str);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,'http://api.qiniu.com/pfop/');
            $header[] = "Content-Type: application/x-www-form-urlencoded";
            $header[] = "Authorization: QBox $access_token";
            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
            curl_setopt ( $ch, CURLOPT_POST, 1 );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS,$query);
            $res = curl_exec($ch);
            curl_close($ch);
            $res = json_decode($res,true);
            if(isset($res['persistentId'])){
                return true;
            }else{
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        

    }

    //URL安全的Base64编码
    public static function urlsafe_base64_encode($str){
        $find = array('+', '/');
        $replace = array('-', '_');
        $encodedSign = str_replace($find, $replace, base64_encode($str));
        return $encodedSign;
    }

    public static function array_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
        if(is_array($arrays)){   
            foreach ($arrays as $array){   
                if(is_array($array)){   
                    $key_arrays[] = $array[$sort_key];   
                }else{   
                    return false;   
                }   
            }   
        }else{   
            return false;   
        }  
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);   
        return $arrays;   
    } 

    public static function generate_token($access_key, $secret_key, $data){
        $digest = hash_hmac("sha1", $data, $secret_key, true);
        // var_dump(bin2hex($digest));die();
        // var_dump($this->urlsafe_base64_encode($digest));die();
        // var_dump($access_key.':'.$this->urlsafe_base64_encode($digest));die();
        return $access_key.':'.self::urlsafe_base64_encode($digest);
    }

    public static function dstrpos($string, $arr, $returnvalue = false) {
        if(empty($string)) return false;
        foreach((array)$arr as $v) {
            if(strpos($string, $v) !== false) {
                $return = $returnvalue ? $v : true;
                return $return;
            }
        }
        return false;
    }

    public static function getQiniuImage($url,$width,$height){
        return "$url?imageView2/1/w/{$width}/h/{$height}";
    }

    //火星系坐标转化成百度坐标
    public static function  GCJTobaidu($lat, $lng){
        $v = M_PI * 3000.0 / 180.0;
        $x = $lng;
        $y = $lat;

        $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $v);
        $t = atan2($y, $x) + 0.000003 * cos($x * $v);

        return array(
            'lat' => $z * sin($t) + 0.006,
            'lng' => $z * cos($t) + 0.0065
        );
    }

    //百度坐标转换成火星系坐标
   public static  function baiduToGCJ($lat, $lng){
        $v = M_PI * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;

        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $v);
        $t = atan2($y, $x) - 0.000003 * cos($x * $v);

        return array(
            'lat' => $z * sin($t),
            'lng' => $z * cos($t)
        );
    }
}
