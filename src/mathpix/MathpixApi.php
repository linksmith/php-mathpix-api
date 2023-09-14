<?php
namespace Yangcp\mathpix;




use GuzzleHttp\RequestOptions;
use Yangcp\helper\Func;

/**
 *
 * User: YCP
 * Date: 2021/9/30
 * Class MathpixApi
 */
class MathpixApi{

    //保存全局实例
    private static $instance;
    private $config = [];
    private function __construct($config)
    {
        $this->config = $config;
    }

    //防止克隆对象
    private function __clone(){}

    public static  function getInstance($config){
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * @param $src
     * @return array
     */
    public function request($src)
    {
        $url = 'https://api.mathpix.com/v3/latex';
        $options = [
            'json' => [
                "src" => $src, // data
//                "data_options" => ["include_asciimath" => true],
            ],
            'headers' => [
                "app_id" => $this->config['app_id'],
                "app_key" => $this->config['app_key'],
                "Content-type" => "application/json"
            ]
        ];
        return Func::guzzle_request($url, 'post', $options);
    }

    /**
     * @param $src
     * @return array
     */
    public function request_pdf($src)
    {
        $url = 'https://api.mathpix.com/v3/pdf';

//        $data = base64_encode(file_get_contents($src));
        $options = [
            RequestOptions::HEADERS => [
                "app_id" => $this->config['app_id'],
                "app_key" => $this->config['app_key'],
            ],
            RequestOptions::MULTIPART => [
                [
                    'name' => 'file',
                    'contents' => fopen($src, 'r')
                ],
                [
                    'name' => 'options_json',
                    'contents' => json_encode([
                        "conversion_formats" => [
                            "docx" => true,
                            "tex.zip" => true
                        ],
                        "math_inline_delimiters" => ["$", "$"],
                        "rm_spaces" => true
                    ])
                ]
            ]
        ];

        return Func::guzzle_request($url, 'post', $options);
    }
}
