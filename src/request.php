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
     * @param string $value [description]
     */
    public function post($options = '') {
        if (isset($options['data'])) {
            $options['data'] = http_build_query($options['data']);
        }
        $options = array_merge_recursive($options, array(
            'method'  => 'POST',
            'headers' => 0,
        ));
        return $this->send($options);
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

        return $this->send($options);
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
