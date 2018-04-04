<?php
/**
 * this7 PHP Framework
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2016-2018 Yan TianZeng<qinuoyun@qq.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://www.ub-7.com
 */
namespace this7\request;

class request {

    /**
     * GET请求获取数据信息
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function get($options) {
        $options['method'] = 'GET';
        return self::send($options);
    }

    /**
     * POST请求获取数据信息
     * @param  string $url  提交的URL地址
     * @param  array  $data 提交的POST数据
     * @param  string $type 数据提交类型 json array
     * @return data         返回对应数据
     */
    public function post($url = '', $data = array(), $type = "json") {
        $ch = curl_init(); //初始化curl
        switch ($type) {
        case 'array':
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            break;
        case 'json':
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            break;
        }
        $data = curl_exec($ch); //运行curl
        curl_close($ch); //关闭curl
        return $data;
    }

    /**
     * Json数据POST请求
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function jsonPost($options) {
        if (isset($options['data'])) {
            $options['data'] = json_encode($options['data']);
        }

        $options = array_merge_recursive($options, array(
            'method'  => 'POST',
            'headers' => array('Content-Type: application/json; charset=utf-8'),
        ));

        return self::send($options);
    }

    /**
     * 发送请求
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function send($options) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);
        curl_setopt($ch, CURLOPT_URL, $options['url']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if (isset($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        if (isset($options['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $options['timeout']);
        }

        if (isset($options['data'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['data']);
        }

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $body = json_decode($result, TRUE);
        if ($body === NULL) {
            $body = $result;
        }

        curl_close($ch);
        return compact('status', 'body');
    }
}
