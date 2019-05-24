<?php

return [
    'login_type' => [
        'mobile'       => 'mobile',
        'username'     => 'username',
        'wx'           => 'wx',
        'wxPC'         => 'wxPC',
        'miniProgram'  => 'miniProgram',
    ],

    'tokenSignSalt' => 'mart2018',
    'tokenTTL' => 30*24*60*60,
    'idTokenTTL' => 15*60,

    'url' => [
        'test'   => 'http://www.baidu.com',
        'formal' => 'http://www.baidu.com',
    ],

    'appid' => [
        'openid' => 'wx06e97f4ed4ac07e3',
        'unionid' => 'wxf62ca307a8f76a6e',
    ],

    'secret' => [
        'openid' => '280f90e36245db75525f990a3ce88867',
        'unionid' => '6c4a4d864f99cc3be5321da8b53ee46f',
    ],

    /*'headimgurl' => 'http://thirdwx.qlogo.cn/mmopen/vi_32/2ktrR9Y4H6zwCzw0Cr4ARHK5SqxiaHbWH6vnH26phN57micfXhmYQBiaNia4OoeIozUgGWicXKzczwD7Lwak3r4gshQ/132',*/

    'apiUrl' => [
        'test'    => 'http://proj6.thatsmags.com',
        'formal'  => 'http://api.mall.thatsamgs.com',
    ],

    'clientUrl' => [
        'test'    => 'http://v.thatsmags.com/',
        'client'  => 'http://api.mall.thatsamgs.com/',
    ],

    /* wapalipay 移动端*/
    'wapalipay' => [
        //应用ID,您的APPID。
        'app_id' => "2016121204186032",
        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key' => "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCgHTRijx1c/2YQ4VZkIyMKymRBdnbPdECsdQ9/8FgXeWG0RjLzkO/BD+LEwqnHpVzvSscquhocEH1GaxCskUEqYaF9gDpEEjCHudz3FRuNjs08jsrJxRYnhleiKE0G+U+XqrUrLM5yd6DT3AXYQnzGtEZu6sLSypmX5IMitZAORXR9P5yVNFSp2NLT4uAJLhZvQWGkTIozIUQY5c5qQUCYH8qL28TOTMCayUVEyuxNpwfCKRKXrdaU0oCN3V3SUzvMaGYrrv7AD7xU1wwiTQ+fY2q0GbGj5eJp0xCfVrU0QSDdiin4qtMkU82SETwh6HIsofZVZMbsR8powLDdNNJPAgMBAAECggEBAJ7KN+Ci9Ej9lViaUZY/7onODL2LYer97QHbmkKUtpiZLZeeovtBOzUprwjZ0Y0I53D9pSYvqKM6izMiDUhHSexhJMoVODO4Il8IqiZ0zX2HKO2s41pVVlJeflx4QWPwLspIZyHpbtjA4UQbCcTes2ZQ9SUdN5fbi5XC73alv7cWABCX5/lPWUBLNFOGBAkTE0glH6kijKYNtDd5LVibGLilirBQDhvRLFCF1D5AZXilIyflmG8UNk3xGRFvNrPIgblM+JtLE1hpUkHmuI8VIl0UT9oMT2jM1Xw2OSIpk+yBk5yc4XBs4nNowNz7i1M/vVC33B06ZcTMM+fHuG3HK2ECgYEA5LRWjFGXjRBuA3J25TBZAlH5QE29dj+kPwYVp/QhTpXOXMlBCrOYJgSmBazS7Og8KXorhB3VwbFrubsNzdLLcy98TRd28+aBXxXDiS938/DpmjcpTppzqcICV4vfAfq298+Js9fRWDOefz65v1Af1MBMMbRf8LUdIADwIhCOn3ECgYEAszk1aQoAtjkNtgQXZ4551O2H0Byle4O+hTl0asi6EGDAu6RWBHsC18vLf2KyyGco8SjaAAEWPd3Yom1c5IgtWP046YBo/6txPwsGiRD1E8uYrcg8WRtZ0MtH9rhacE0G12xNQFrn2Gv3lR0zqtAwLbfQGVDmAhGlZxSN+uSVLb8CgYBmue4KFvgIn7GakMaAyYeheCqJzKFmRM3ElToS96AiST2pBajWYrbblMjx0Z/oU6P6SWrUbAZAey3U/gUER0OlGFYv1nNSuF2x1PfAXfb2NmbnIxHFwbBkNsWQhz4DCJc4lhrXEgBxKrZtl0IdgaLakAlpZgiV+PP9FQ7HbkJxsQKBgQCesxnIWUMoGH07n4PZ/x+CnJKWhcdDB/W8opOjuvqHZiVEAtDoRsTNsXgQ5KTLMA3g/fuL5Wp8feVGbvDCrJL7Kb8rhLl1K6qr2GeBF7LsRGx99cD6Zm2xpU+j+LqclphoSU5eniCOU4x4TdNifdcrpIhw4mHJkzaTgG/4qlAuoQKBgCiWiGw8B/IDf87FBkwbHEvjZooWa9YWmN1xUyDppcmo6SooVEDXklZEFFFiDMImQg3hsPoGQiT1ztawccg2FIrAk7P8Yqmeey7HCthd94Ef70VTIB40vyvDzzaJUPIkdCHM5HkzVj74YUVDZxlwT19wGDLQB8NoTyAQgNwFoxLH",
        //异步通知地址
        'notify_url' => "http://proj6.thatsmags.com/thmartApi/Alipay/notify",
        //同步跳转
        'return_url' => "http://proj6.thatsmags.com/thmartApi/Alipay/return",
        //编码格式
        'charset' => "UTF-8",
        //签名方式
        'sign_type'=>"RSA2",
        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAoDNsGUNXsni6aGm+SE5tPQ2Qzen7Xz5INA34Uzq1NXIxMXdBPxw+52abTTFsYJxP66XqQxvWSzOJPsdZb13RNawDw0m7997V44O2hu3FCCAobTtOmJ60asmYm1bd1hB8+Qb0obDdveIedvrxX05zlEB24j9U9hfDuu8KCa/iMF4FfttwHUfsHW+85b7aAO2mAB7k74KOpnlEKDeDDpDd/GUcI2neRNfnCX4cK7FPzffYh76eNbeF02os36L13cHqthCOiIjsslA2ZRRpnxjlRsRXCOV8aaRd8cu8tIeU/utzSndg29hJdMQjTBTCa+LgqtKuj+qJlQ4TccbMV/EPIwIDAQAB",
    ],

    /* wabalipay pc端*/
    'webalipay1' => [
        //应用ID,您的APPID。
        'app_id' => "2016121204186032",
        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key' => "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCgHTRijx1c/2YQ4VZkIyMKymRBdnbPdECsdQ9/8FgXeWG0RjLzkO/BD+LEwqnHpVzvSscquhocEH1GaxCskUEqYaF9gDpEEjCHudz3FRuNjs08jsrJxRYnhleiKE0G+U+XqrUrLM5yd6DT3AXYQnzGtEZu6sLSypmX5IMitZAORXR9P5yVNFSp2NLT4uAJLhZvQWGkTIozIUQY5c5qQUCYH8qL28TOTMCayUVEyuxNpwfCKRKXrdaU0oCN3V3SUzvMaGYrrv7AD7xU1wwiTQ+fY2q0GbGj5eJp0xCfVrU0QSDdiin4qtMkU82SETwh6HIsofZVZMbsR8powLDdNNJPAgMBAAECggEBAJ7KN+Ci9Ej9lViaUZY/7onODL2LYer97QHbmkKUtpiZLZeeovtBOzUprwjZ0Y0I53D9pSYvqKM6izMiDUhHSexhJMoVODO4Il8IqiZ0zX2HKO2s41pVVlJeflx4QWPwLspIZyHpbtjA4UQbCcTes2ZQ9SUdN5fbi5XC73alv7cWABCX5/lPWUBLNFOGBAkTE0glH6kijKYNtDd5LVibGLilirBQDhvRLFCF1D5AZXilIyflmG8UNk3xGRFvNrPIgblM+JtLE1hpUkHmuI8VIl0UT9oMT2jM1Xw2OSIpk+yBk5yc4XBs4nNowNz7i1M/vVC33B06ZcTMM+fHuG3HK2ECgYEA5LRWjFGXjRBuA3J25TBZAlH5QE29dj+kPwYVp/QhTpXOXMlBCrOYJgSmBazS7Og8KXorhB3VwbFrubsNzdLLcy98TRd28+aBXxXDiS938/DpmjcpTppzqcICV4vfAfq298+Js9fRWDOefz65v1Af1MBMMbRf8LUdIADwIhCOn3ECgYEAszk1aQoAtjkNtgQXZ4551O2H0Byle4O+hTl0asi6EGDAu6RWBHsC18vLf2KyyGco8SjaAAEWPd3Yom1c5IgtWP046YBo/6txPwsGiRD1E8uYrcg8WRtZ0MtH9rhacE0G12xNQFrn2Gv3lR0zqtAwLbfQGVDmAhGlZxSN+uSVLb8CgYBmue4KFvgIn7GakMaAyYeheCqJzKFmRM3ElToS96AiST2pBajWYrbblMjx0Z/oU6P6SWrUbAZAey3U/gUER0OlGFYv1nNSuF2x1PfAXfb2NmbnIxHFwbBkNsWQhz4DCJc4lhrXEgBxKrZtl0IdgaLakAlpZgiV+PP9FQ7HbkJxsQKBgQCesxnIWUMoGH07n4PZ/x+CnJKWhcdDB/W8opOjuvqHZiVEAtDoRsTNsXgQ5KTLMA3g/fuL5Wp8feVGbvDCrJL7Kb8rhLl1K6qr2GeBF7LsRGx99cD6Zm2xpU+j+LqclphoSU5eniCOU4x4TdNifdcrpIhw4mHJkzaTgG/4qlAuoQKBgCiWiGw8B/IDf87FBkwbHEvjZooWa9YWmN1xUyDppcmo6SooVEDXklZEFFFiDMImQg3hsPoGQiT1ztawccg2FIrAk7P8Yqmeey7HCthd94Ef70VTIB40vyvDzzaJUPIkdCHM5HkzVj74YUVDZxlwT19wGDLQB8NoTyAQgNwFoxLH",
        //异步通知地址
        'notify_url' => "http://proj6.thatsmags.com/thmartApi/Alipay/notifyurlPc",
        //同步跳转
        'return_url' => "http://proj6.thatsmags.com/thmartApi/Alipay/returnurlPc",
        //编码格式
        'charset' => "UTF-8",
        //签名方式
        'sign_type'=>"RSA2",
        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAoDNsGUNXsni6aGm+SE5tPQ2Qzen7Xz5INA34Uzq1NXIxMXdBPxw+52abTTFsYJxP66XqQxvWSzOJPsdZb13RNawDw0m7997V44O2hu3FCCAobTtOmJ60asmYm1bd1hB8+Qb0obDdveIedvrxX05zlEB24j9U9hfDuu8KCa/iMF4FfttwHUfsHW+85b7aAO2mAB7k74KOpnlEKDeDDpDd/GUcI2neRNfnCX4cK7FPzffYh76eNbeF02os36L13cHqthCOiIjsslA2ZRRpnxjlRsRXCOV8aaRd8cu8tIeU/utzSndg29hJdMQjTBTCa+LgqtKuj+qJlQ4TccbMV/EPIwIDAQAB",
    ],

    /* webalipay pc端alipay*/
    'webalipay' => [
        'partner' => '2088301524559921',
        'seller_id' => '2088301524559921',
        'key' => 'agh36vdil1cwuaecr0bmbpjbxksabafd',
        'notify_url' => 'http://proj6.thatsmags.com/thmartApi/Alipay/notifyurlPc',
        'return_url' => 'http://proj6.thatsmags.com/thmartApi/Alipay/returnurlPc',
        'sign_type' => strtoupper('MD5'),
        'input_charset' => strtolower('utf-8'),
        'cacert' => '',
        'transport' => 'http',
        'payment_type' => '1',
        'service' => 'create_direct_pay_by_user',
        'anti_phishing_key' => '',
        'exter_invoke_ip' => '',
    ],

    'phpmail' => [
        'port'       => '465',
        'host'       => 'smtp.exmail.qq.com',
        'username'   => 'donotreply@urbanatomy.com',
        'password'   => 'Donot2017',
        'from'       => 'donotreply@urbanatomy.com',
        'fromname'   => 'donotreply_urbanatomy',
        'smtpsecure' => 'ssl',
        'charset'    => "utf-8",
    ],

    'headimg' => 'http://api.mall.thatsmags.com/Public/ckfinder/images/thmart.png',

    'wxNotifyUrl' => 'http://proj6.thatsmags.com/thmartApi/Wx/notify',
];
