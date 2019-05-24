<?php
/**
 * 无限极分类
 */
use Illuminate\PHPMailer\PHPMailer;

function loop($data, $id=0)
{
    $list = array();
    foreach ($data as &$v) {
        if ($v['fname'] == $id) {
            $v['son'] = loop($data, $v['id']);
            if (empty($v['son'])) {
                unset($v['son']);
            }
            array_push($list, $v);
        }
    }
    return $list;
}

/**
 * 获取某一级分类的所有子类id
 */
function sonIdArray($data, $id=0)
{
    $id_array = [];
    foreach ($data as &$v) {
        if ($v['fname'] == $id) {
            $array = sonIdArray($data, $v['name']);
            foreach ($array as &$value) {
                array_push($id_array, $value);
            }
            array_push($id_array, $v['name']);
        }
    }
    return array_unique($id_array);
}

/**
 * 输出json格式数据
 */
function returnJson($code, $message, $data=null)
{
    header('Content-Type:application/json; charset=utf-8');
    header('Access-Control-Allow-Origin:*');   // 指定允许其他域名访问 
    header('Access-Control-Allow-Headers:TOKEN,x-requested-with,content-type,X-Requested-With,X_Requested_With,Accept,Bearer');// 响应头设置
    $data = [
        'code' => $code, 
        'message' => $message, 
        'data' => $data
    ];
    exit(json_encode($data));
}

/**
 * 获取网站域名
 */
function adminDomain()
{
    $server = $_SERVER['HTTP_HOST'];
    if ($server == 'proj6.thatsmags.com') {
        return 'http://proj6.thatsmags.com';
    } elseif ($server == 'api.mall.thatsmags.com') {
        return 'http://api.mall.thatsmags.com';
    } else {
        return 'http://proj6.thatsmags.com';
    }
}

/**
 * 获取网站域名
 */
function apiDomain()
{
    return 'http://'.$_SERVER['HTTP_HOST'].'/';
}

/**
 * 密码加密
 */
function md5Password($password, $salt)
{
    return md5(md5($password).$salt);
}

/**
 * 生成token
 */
function createToken($id)
{
    $string = base64_encode((time() + config('config.tokenTTL')).$id);
    return md5($string).config('config.tokenSignSalt');
}

/**
 * 批量修改图片路径
 */
function convertUrl($array)
{
    foreach ($array as &$v) {
        $v['pic'] = adminDomain().$v['pic'];
    }
    unset($v);
    return $array;
}

/**
 * 批量修改时间戳为时间
 */
function convertTime($array)
{
    foreach ($array as &$v) {
        $v['createTime'] = date('Y-m-d', $v['createTime']);
    }
    unset($v);
    return $array;
}

/**
 * 对象转换成数组
 */
function objectToArray($object)
{
    $object = json_encode($object);
    $object = json_decode($object, true); 
    return $object;
}

/**
 * 正则替换富文本编辑器中的图片相对路径为绝对路径
 */
function contentConvertUrl($param)
{
    $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/"; 
    preg_match_all($pattern, $param, $match); 
    foreach ($match['1'] as &$v) {
        $url = adminDomain().$v;
        $param = str_replace($v, $url, $param);
    }
    unset($v);
    return $param;
}

function https_request($url, $data = null, $cookie = null) 
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if ($cookie) curl_setopt($curl, CURLOPT_HTTPHEADER, array('Cookie:laravel_session='.$cookie['laravel_session']));//在请求头中写入cookie并发送
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/* PHP CURL HTTPS POST */
function curl_post_https($url,$data=null,$cookie=null){ // 模拟提交数据函数
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    /*curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在*/
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0);
    if ($cookie) curl_setopt($curl, CURLOPT_HTTPHEADER, array('Cookie:laravel_session='.$cookie['laravel_session']));//在请求头中写入cookie并发送
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据，json格式
}

/**
 * 生成订单
 */
function makeOrder() 
{
    return date('y', time()).mt_rand(1000000000, 9999999999);
}

/**
 * 手动生成分页数据
 */
function pageData($res, $page, $pageSize) 
{
    $count = count($res);
    $totalPage = ceil($count/$pageSize);
    if (isset($res)) {
        $res = [
            'data'      => array_slice($res, ($page-1)*$pageSize, $pageSize),
            'totalPage' => $totalPage,
        ];
    } else {
        $res = [];
    }
    return $res;
}

/*
 * 微信银行卡支付
 */
function wxCardPay($eventName, $money, $trade_no)
{
    $tools = new JsApiPay();
    $openId2 = $tools->GetOpenid();
    $input2 = new WxPayUnifiedOrder();
    $input2->SetBody($eventName);
    $input2->SetAttach("test");
    $input2->SetOut_trade_no($trade_no);
    $input2->SetTotal_fee($money);
    $input2->SetTime_start(date("YmdHis"));
    $input2->SetTime_expire(date("YmdHis", time() + 600));
    $input2->SetGoods_tag("tag");
    $url = "http://proj6.thatsmags.com/thmartApi/Wx/notify";
    $input2->SetNotify_url($url);
    $input2->SetTrade_type("JSAPI");
    $input2->SetOpenid($openId2);
    $api = new \Illuminate\Payment\Wxpaylib\WxPayApi();
    $order = $api->unifiedOrder($input2);
    /*return $order;*/
    $jsApiParameters = $tools->GetJsApiParameters($order);
    return $jsApiParameters;
}

/*
 * phpmail发送邮件
 */
function sendMail($subject, $body, $email)
{
    $mail = new PHPMailer(); //实例化
    $mail->IsSMTP();
    $mail->SMTPAuth   = true;
    $mail->Port       = config('config.phpmail.port');
    $mail->Host       = config('config.phpmail.host');
    $mail->Username   = config('config.phpmail.username');
    $mail->Password   = config('config.phpmail.password');
    $mail->From       = config('config.phpmail.from');
    $mail->FromName   = config('config.phpmail.fromname');
    $mail->SMTPSecure = config('config.phpmail.smtpsecure');
    $mail->CharSet    = config('config.phpmail.charset');
    $mail->IsHTML(true);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->ClearAddresses();
    $mail->AddAddress($email);
    return($mail->Send());
}

/*
 * 存储base64图片地址
 */
function base64_image_content($base_img, $relative_url)
{
    preg_match('/^(data:\s*image\/(\w+);base64,)/', $base_img, $result);
    $base_img = str_replace($result['1'], '', $base_img);
    // 设置文件路径和文件前缀名称
    $path = adminDomain().'/'.$relative_url;
    $prefix = 'nx_';
    $output_file = $prefix.time().rand(100000,999999).'.jpg';
    $path = $path.$output_file;
    file_put_contents($relative_url.$output_file, base64_decode($base_img));
    //输出文件
    return '/'.$relative_url.$output_file;
}

/*
 * 后台api报错代码
 */
function returnFalse($data)
{
    $data = array_values(objectToArray($data));
    $message = '';
    foreach ($data as $v) {
        $message .= $v['0'].'</br> ';
    }
    returnJson(0, $message);
}
