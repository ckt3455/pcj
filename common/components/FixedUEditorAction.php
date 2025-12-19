<?php

namespace common\components;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\UploadedFile;
use yii\helpers\Url;

class FixedUEditorAction extends Action
{
    public $config = [];

    public function init()
    {
        // 清理所有输出缓冲区
        $this->cleanAllOutputBuffers();

        // close csrf
        Yii::$app->request->enableCsrfValidation = false;

        // 默认设置
        $_config = require(Yii::getAlias('@vendor/kucha/ueditor/config.php'));

        // load config file
        $this->config = ArrayHelper::merge($_config, $this->config);

        parent::init();

        // 禁用 Yii 的自动输出
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    }

    public function run()
    {
        // 清理所有缓冲区
        $this->cleanAllOutputBuffers();

        // 获取处理结果
        $result = $this->processRequest();

        // 直接输出并退出
        exit($result);
    }

    /**
     * 清理所有输出缓冲区
     */
    protected function cleanAllOutputBuffers()
    {
        // 清理所有输出缓冲区
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }
        // 开启新的缓冲区并立即清理
        @ob_start();
        @ob_end_clean();
    }

    /**
     * 处理请求
     */
    protected function processRequest()
    {
        // 再次清理，确保万无一失
        $this->cleanAllOutputBuffers();

        $action = Yii::$app->request->get('action');

        switch ($action) {
            case 'config':
                $data = $this->config;
                break;

            case 'uploadimage':
            case 'uploadscrawl':
            case 'uploadvideo':
            case 'uploadfile':
                $data = $this->doUpload($action);
                break;

            case 'listimage':
            case 'listfile':
                $data = $this->doList($action);
                break;

            case 'catchimage':
                $data = $this->doCrawler();
                break;

            default:
                $data = ['state' => '请求地址出错'];
                break;
        }

        // 转换为 JSON
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        // 处理 JSONP
        if (isset($_GET["callback"]) && !empty($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                // 直接设置头部并输出
                header('Content-Type: application/javascript; charset=utf-8');
                return htmlspecialchars($_GET["callback"], ENT_QUOTES, 'UTF-8') . '(' . $json . ')';
            } else {
                $errorData = ['state' => 'callback参数不合法'];
                $errorJson = json_encode($errorData, JSON_UNESCAPED_UNICODE);
                header('Content-Type: application/json; charset=utf-8');
                return $errorJson;
            }
        } else {
            header('Content-Type: application/json; charset=utf-8');
            return $json;
        }
    }

    /**
     * 处理上传 - 调用本地 file2/upload-images 方法（简化版）
     */
    protected function doUpload($action)
    {
        // 只处理图片上传
        if ($action !== 'uploadimage' && $action !== 'uploadscrawl') {
            // 其他类型使用原 UEditor 逻辑
            require_once Yii::getAlias('@vendor/kucha/ueditor/Uploader.php');

            $base64 = "upload";
            switch ($action) {
                case 'uploadimage':
                    $config = [
                        "imageRoot" => $this->config['imageRoot'],
                        "pathFormat" => $this->config['imagePathFormat'],
                        "maxSize" => $this->config['imageMaxSize'],
                        "allowFiles" => $this->config['imageAllowFiles']
                    ];
                    $fieldName = $this->config['imageFieldName'];
                    break;
                case 'uploadscrawl':
                    $config = [
                        "imageRoot" => $this->config['imageRoot'],
                        "pathFormat" => $this->config['scrawlPathFormat'],
                        "maxSize" => $this->config['scrawlMaxSize'],
                        "allowFiles" => $this->config['scrawlAllowFiles'],
                        "oriName" => "scrawl.png"
                    ];
                    $fieldName = $this->config['scrawlFieldName'];
                    $base64 = "base64";
                    break;
                case 'uploadvideo':
                    $config = [
                        "imageRoot" => $this->config['imageRoot'],
                        "pathFormat" => $this->config['videoPathFormat'],
                        "maxSize" => $this->config['videoMaxSize'],
                        "allowFiles" => $this->config['videoAllowFiles']
                    ];
                    $fieldName = $this->config['videoFieldName'];
                    break;
                case 'uploadfile':
                default:
                    $config = [
                        "imageRoot" => $this->config['imageRoot'],
                        "pathFormat" => $this->config['filePathFormat'],
                        "maxSize" => $this->config['fileMaxSize'],
                        "allowFiles" => $this->config['fileAllowFiles']
                    ];
                    $fieldName = $this->config['fileFieldName'];
                    break;
            }

            $up = new \kucha\ueditor\Uploader($fieldName, $config, $base64);
            return $up->getFileInfo();
        }

        try {
            // 处理图片上传（调用本地接口）
            if ($action === 'uploadimage') {
                return $this->handleImageUpload();
            } elseif ($action === 'uploadscrawl') {
                return $this->handleScrawlUpload();
            }

        } catch (\Exception $e) {
            Yii::error("UEditor 上传失败: {$e->getMessage()}", 'ueditor');
            return ['state' => '上传失败: ' . $e->getMessage()];
        }
    }

    /**
     * 处理列表
     */
    protected function doList($action)
    {
        // 获取配置参数
        $configKey = ($action === 'listimage') ? 'imageManager' : 'fileManager';

        $listPath = $this->config[$configKey . 'ListPath'] ?? '/uploads/ueditor/';
        $listSize = (int) ($this->config[$configKey . 'ListSize'] ?? 20);
        $urlPrefix = $this->config[$configKey . 'UrlPrefix'] ?? '';
        $allowFiles = $this->config[$configKey . 'AllowFiles'] ?? ['.png', '.jpg', '.jpeg', '.gif', '.bmp'];

        // 获取分页参数
        $start = (int) Yii::$app->request->get('start', 0);
        $size = (int) Yii::$app->request->get('size', $listSize);

        // 将允许的文件类型转换为正则表达式
        $allowExt = array_map(function($ext) {
            return preg_quote(trim($ext, '.'), '/');
        }, $allowFiles);

        $allowPattern = '/\.(' . implode('|', $allowExt) . ')$/i';

        // 构建完整路径
        $basePath = Yii::getAlias('@webroot');
        $fullListPath = $basePath . $listPath;

        // 确保目录存在
        if (!is_dir($fullListPath) && !mkdir($fullListPath, 0755, true)) {
            Yii::error("图片列表目录不存在且创建失败: {$fullListPath}", 'ueditor');
            return [
                'state' => '目录不存在',
                'list' => [],
                'start' => $start,
                'total' => 0
            ];
        }

        // 获取文件列表
        $files = [];
        $total = 0;

        if (is_dir($fullListPath)) {
            $this->getFilesRecursive($fullListPath, $basePath, $allowPattern, $files, $total);

            // 按修改时间排序（最新在前）
            usort($files, function($a, $b) {
                return $b['mtime'] <=> $a['mtime'];
            });

            // 分页
            $resultList = array_slice($files, $start, $size);

            return [
                'state' => empty($resultList) ? '没有找到文件'.$fullListPath : 'SUCCESS',
                'list' => $resultList,
                'start' => $start,
                'total' => $total
            ];
        }

        return [
            'state' => '目录不存在',
            'list' => [],
            'start' => $start,
            'total' => 0
        ];
    }

    /**
     * 递归获取文件
     */
    protected function getFilesRecursive($dir, $basePath, $pattern, &$files, &$total)
    {
        if (!is_dir($dir)) {
            return;
        }

        $handle = opendir($dir);
        if ($handle === false) {
            return;
        }

        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                // 递归子目录
                $this->getFilesRecursive($path, $basePath, $pattern, $files, $total);
            } else {
                // 检查文件类型
                if (preg_match($pattern, $file)) {
                    // 构建相对路径
                    $relativePath = str_replace($basePath, '', $path);
                    $relativePath = str_replace('\\', '/', $relativePath);

                    // 构建URL
                    $url = Yii::$app->request->hostInfo . $relativePath;

                    $files[] = [
                        'url' => $url,
                        'mtime' => filemtime($path)
                    ];
                    $total++;
                }
            }
        }

        closedir($handle);
    }
    /**
     * 处理远程抓取
     */
    protected function doCrawler()
    {
        // 这里简化处理
        return [
            'state' => 'SUCCESS',
            'list' => []
        ];
    }


    /**
     * 处理图片上传
     */
    protected function handleImageUpload()
    {
        $file = UploadedFile::getInstanceByName('upfile');

        if (!$file) {
            return ['state' => '没有选择文件'];
        }

        // 临时保存文件
        $tempPath = sys_get_temp_dir() . '/ueditor_' . uniqid() . '_' . $file->name;
        if (!$file->saveAs($tempPath)) {
            return ['state' => '临时文件保存失败'];
        }

        try {
            // 使用 CURL 调用本地接口
            $ch = curl_init();

            $postData = [
                'file' => new \CURLFile($tempPath, $file->type, $file->name)
            ];

            curl_setopt_array($ch, [
                CURLOPT_URL => Url::to(['/file2/upload-images'], true),
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new \Exception("CURL 错误: {$error}");
            }

            $result = Json::decode($response);

            // 格式化响应
            if (isset($result['state']) && $result['state'] === 'success') {
                return [
                    'state' => 'SUCCESS',
                    'url' => $result['url'],
                    'title' => basename($result['url']),
                    'original' => $result['imgName'] ?? basename($result['url']),
                    'type' => '.' . pathinfo($result['url'], PATHINFO_EXTENSION),
                    'size' => $result['size'] ?? 0,
                ];
            } else {
                return [
                    'state' => $result['msg'] ?? '上传失败',
                    'url' => '',
                    'title' => '',
                    'original' => '',
                    'type' => '',
                    'size' => 0,
                ];
            }

        } catch (\Exception $e) {
            throw $e;
        } finally {
            // 清理临时文件
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }
}