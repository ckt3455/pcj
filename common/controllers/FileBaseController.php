<?php
namespace common\controllers;

use yii;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\web\Response;
/**
 * Class FileBaseController
 * @package backend\controllers
 * 文件图片上传控制器
 */
abstract class FileBaseController extends yii\web\Controller
{
    public $enableCsrfValidation = false;//关闭csrf验证


    /**
     * @var array 上传状态映射表
     */
    protected array $uploadState = [];

    /**
     * @var array 图片上传配置
     */
    protected array $uploadConfig = [];

    /**
     * @var array 压缩配置
     */
    protected array $compressConfig = [];

    /**
     * @var array 缩略图配置
     */
    protected array $thumbnailConfig = [];

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // 初始化配置
        $this->initializeConfig();
    }

    /**
     * 初始化配置
     * @throws InvalidConfigException
     */
    protected function initializeConfig(): void
    {
        $params = Yii::$app->params;

        if (!isset($params['uploadState'])) {
            throw new InvalidConfigException('uploadState配置不存在');
        }

        if (!isset($params['imagesUpload'])) {
            throw new InvalidConfigException('imagesUpload配置不存在');
        }

        $this->uploadState = $params['uploadState'];
        $this->uploadConfig = $params['imagesUpload'];

        // 设置压缩配置
        $this->compressConfig = ArrayHelper::merge([
            'enabled' => true,
            'maxWidth' => 1920,
            'maxHeight' => 1080,
            'quality' => 85,
            'pngQuality' => 9,
            'webpQuality' => 80,
            'minFileSize' => 102400,
            'types' => ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp'],
        ], $this->uploadConfig['compress'] ?? []);

        // 设置缩略图配置
        $this->thumbnailConfig = ArrayHelper::merge([
            'enabled' => true,
            'width' => 300,
            'height' => 200,
            'quality' => 80,
            'suffix' => '_thumb',
            'types' => ['.jpg', '.jpeg', '.png'],
        ], $this->uploadConfig['thumbnail'] ?? []);
    }

    /**
     * 图片上传方法
     */

    public function actionUploadImages(): Response
    {
        // 设置响应格式
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 默认返回状态
        $result = [
            'flg' => 2,
            'msg' => $this->uploadState['ERROR_UNKNOWN'],
            'imgName' => '',
            'url' => '',
            'state' => 'error',
            'originalSize' => 0,
            'compressedSize' => 0,
            'compressionRatio' => 0.0,
        ];

        try {
            // 验证文件上传
            if (!isset($_FILES['file']) || empty($_FILES['file']['name'])) {
                $result['msg'] = $this->uploadState['ERROR_FILE_NOT_FOUND'];
                return $this->asJson($result);
            }

            $file = $_FILES['file'];

            // 检查上传错误
            $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;
            if ($uploadError !== UPLOAD_ERR_OK) {
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE => $this->uploadState['ERROR_SIZE_EXCEED'],
                    UPLOAD_ERR_FORM_SIZE => $this->uploadState['ERROR_SIZE_EXCEED'],
                    UPLOAD_ERR_PARTIAL => $this->uploadState['ERROR_FILE_MOVE'],
                    UPLOAD_ERR_NO_FILE => $this->uploadState['ERROR_FILE_NOT_FOUND'],
                    UPLOAD_ERR_NO_TMP_DIR => $this->uploadState['ERROR_CREATE_DIR'],
                    UPLOAD_ERR_CANT_WRITE => $this->uploadState['ERROR_DIR_NOT_WRITEABLE'],
                    UPLOAD_ERR_EXTENSION => $this->uploadState['ERROR_TYPE_NOT_ALLOWED'],
                ];

                $result['msg'] = $uploadErrors[$uploadError] ?? $this->uploadState['ERROR_UNKNOWN'];
                return $this->asJson($result);
            }

            $fileSize = $file['size'];
            $fileName = $file['name'];
            $fileTmp = $file['tmp_name'];

            // 获取文件扩展名
            $fileExtension = $this->getFileExtension($fileName);

            // 验证文件大小
            $maxSize = $this->uploadConfig['imgMaxSize'] ?? (5 * 1024 * 1024);
            if ($fileSize > $maxSize) {
                $result['msg'] = $this->uploadState['ERROR_SIZE_EXCEED'];
                return $this->asJson($result);
            }

            // 验证文件类型
            if (!$this->validateFileType($fileExtension)) {
                $result['msg'] = $this->uploadState['ERROR_TYPE_NOT_ALLOWED'];
                return $this->asJson($result);
            }

            // 生成文件路径
            $relativePath = $this->generateFilePath($fileExtension);
            $fullPath = $this->getFullPath($relativePath);

            // 确保目录存在
            $this->ensureDirectoryExists(dirname($fullPath));

            // 保存文件
            if (!$this->saveUploadedFile($fileTmp, $fullPath)) {
                $result['msg'] = $this->uploadState['ERROR_FILE_MOVE'];
                return $this->asJson($result);
            }

            // 获取原始文件大小
            clearstatcache(true, $fullPath);
            $originalSize = filesize($fullPath);
            $result['originalSize'] = $originalSize;

            // 压缩图片
            $compressedSize = $originalSize;
            $compressResult = null;

            if ($this->shouldCompressImage($fullPath, $fileExtension, $originalSize)) {
                $compressResult = $this->compressImage($fullPath, $fileExtension);

                if ($compressResult['success']) {
                    $compressedSize = $compressResult['size'];
                    $result['compressedSize'] = $compressedSize;
                    $result['compressionRatio'] = round((1 - $compressedSize / $originalSize) * 100, 2);
                    $result['compressInfo'] = $compressResult['info'];
                } else {
                    $result['compressError'] = $compressResult['error'];
                }
            }

            // 生成缩略图
            $thumbnailResult = $this->generateThumbnail($fullPath, $relativePath, $fileExtension);
            if ($thumbnailResult !== null) {
                $result['thumbnail'] = $thumbnailResult;
            }

            // 构建成功响应
            $result = array_merge($result, [
                'flg' => 1,
                'msg' => '上传成功',
                'imgName' => $relativePath,
                'url' => $this->getUrl($relativePath),
                'state' => 'success',
                'size' => $compressedSize,
            ]);

        } catch (\Exception $exception) {
            Yii::error('图片上传失败: ' . $exception->getMessage(), 'upload');
            $result['msg'] = $this->uploadState['ERROR_UNKNOWN'] . ': ' . $exception->getMessage();
        }

        return $this->asJson($result);
    }


    /**
     * 获取文件扩展名
     * @param string $fileName 文件名
     * @return string 扩展名
     */
    protected function getFileExtension(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        return $extension ? '.' . strtolower($extension) : '';
    }

    /**
     * 验证文件类型
     * @param string $extension 文件扩展名
     * @return bool 是否允许
     */
    protected function validateFileType(string $extension): bool
    {
        $allowedTypes = $this->uploadConfig['allowTypes'] ?? ['.jpg', '.jpeg', '.png', '.gif', '.bmp'];
        return in_array(strtolower($extension), $allowedTypes, true);
    }

    /**
     * 生成文件路径
     * @param string $extension 文件扩展名
     * @return string 相对路径
     */
    protected function generateFilePath(string $extension): string
    {
        $datePath = date('Y/m/d/');
        $prefix = $this->uploadConfig['imgPrefix'] ?? '';
        $basePath = $this->uploadConfig['imgPath'] ?? 'images/';

        $randomName = $this->generateRandomName(10);

        return $basePath . $datePath . $prefix . $randomName . $extension;
    }

    /**
     * 获取完整路径
     * @param string $relativePath 相对路径
     * @return string 完整路径
     */
    protected function getFullPath(string $relativePath): string
    {
        return Yii::getAlias('@attachment') . '/' . ltrim($relativePath, '/');
    }

    /**
     * 获取访问URL
     * @param string $relativePath 相对路径
     * @return string URL
     */
    protected function getUrl(string $relativePath): string
    {
        return Yii::getAlias('@attachurl') . '/' . ltrim($relativePath, '/');
    }

    /**
     * 确保目录存在
     * @param string $directory 目录路径
     * @return bool 是否成功
     * @throws Exception 目录创建失败
     */
    protected function ensureDirectoryExists(string $directory): bool
    {
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new Exception('无法创建目录: ' . $directory);
        }
        return true;
    }

    /**
     * 保存上传的文件
     * @param string $tempPath 临时文件路径
     * @param string $destinationPath 目标路径
     * @return bool 是否成功
     */
    protected function saveUploadedFile(string $tempPath, string $destinationPath): bool
    {
        return move_uploaded_file($tempPath, $destinationPath);
    }

    /**
     * 判断是否应该压缩图片
     * @param string $filePath 文件路径
     * @param string $extension 文件扩展名
     * @param int $fileSize 文件大小
     * @return bool 是否应该压缩
     */
    protected function shouldCompressImage(string $filePath, string $extension, int $fileSize): bool
    {
        if (!$this->compressConfig['enabled']) {
            return false;
        }

        if ($fileSize < $this->compressConfig['minFileSize']) {
            return false;
        }

        if (!in_array(strtolower($extension), $this->compressConfig['types'], true)) {
            return false;
        }

        return true;
    }

    /**
     * 压缩图片
     * @param string $filePath 文件路径
     * @param string $extension 文件扩展名
     * @return array 压缩结果
     */
    protected function compressImage(string $filePath, string $extension): array
    {
        $result = [
            'success' => false,
            'size' => 0,
            'info' => '',
            'error' => '',
        ];

        try {
            // 获取图片信息
            $imageInfo = @getimagesize($filePath);
            if ($imageInfo === false) {
                $result['error'] = '无法读取图片信息';
                return $result;
            }

            [$width, $height, $type] = $imageInfo;

            // 检查是否需要调整尺寸
            $newWidth = $width;
            $newHeight = $height;

            $maxWidth = $this->compressConfig['maxWidth'] ?? 1920;
            $maxHeight = $this->compressConfig['maxHeight'] ?? 1080;

            if ($width > $maxWidth || $height > $maxHeight) {
                $scale = min($maxWidth / $width, $maxHeight / $height);

                if ($scale < 1) {
                    $newWidth = (int) floor($width * $scale);
                    $newHeight = (int) floor($height * $scale);
                    $result['info'] .= sprintf("尺寸调整: %dx%d -> %dx%d ", $width, $height, $newWidth, $newHeight);
                }
            }

            // 创建图像资源
            $source = $this->createImageResource($filePath, $type);
            if ($source === null) {
                $result['error'] = '无法创建图像资源';
                return $result;
            }

            // 创建新图像
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            if ($newImage === false) {
                imagedestroy($source);
                $result['error'] = '无法创建新图像';
                return $result;
            }

            // 处理透明背景
            $this->handleImageBackground($newImage, $type);

            // 重采样图像
            imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // 创建临时文件
            $tempFile = $filePath . '.tmp';

            // 保存图像
            $saveResult = $this->saveImage($newImage, $tempFile, $type, $extension);

            // 清理资源
            imagedestroy($source);
            imagedestroy($newImage);

            if (!$saveResult['success']) {
                $result['error'] = $saveResult['error'] ?? '图片保存失败';
                @unlink($tempFile);
                return $result;
            }

            // 获取新文件大小
            $newSize = @filesize($tempFile);
            if ($newSize === false) {
                $result['error'] = '无法获取新文件大小';
                @unlink($tempFile);
                return $result;
            }

            $originalSize = @filesize($filePath);

            // 只有压缩有效果才替换原文件
            if ($newSize < $originalSize) {
                // 备份原文件
//                $backupPath = $filePath . '.bak';
//                copy($filePath, $backupPath);

                // 替换原文件
                if (rename($tempFile, $filePath)) {
                    $result['success'] = true;
                    $result['size'] = $newSize;
                    $result['info'] .= sprintf("压缩率: %.2f%%",
                        (1 - $newSize / $originalSize) * 100);
                } else {
                    $result['error'] = '文件替换失败';
                    @unlink($tempFile);
                }
            } else {
                // 压缩后反而变大，保持原文件
                $result['info'] = '压缩效果不佳，保持原图';
                $result['size'] = $originalSize;
                @unlink($tempFile);
            }

        } catch (\Exception $exception) {
            $result['error'] = '压缩异常: ' . $exception->getMessage();
        }

        return $result;
    }

    /**
     * 创建图像资源
     * @param string $filePath 文件路径
     * @param int $type 图像类型常量
     * @return resource|null 图像资源
     */
    protected function createImageResource(string $filePath, int $type)
    {
        return match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($filePath),
            IMAGETYPE_PNG => $this->createPngImage($filePath),
            IMAGETYPE_GIF => imagecreatefromgif($filePath),
            IMAGETYPE_BMP => function_exists('imagecreatefrombmp') ? imagecreatefrombmp($filePath) : null,
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($filePath) : null,
            default => null,
        };
    }

    /**
     * 创建PNG图像资源
     * @param string $filePath 文件路径
     * @return resource|false PNG图像资源
     */
    protected function createPngImage(string $filePath)
    {
        $image = imagecreatefrompng($filePath);
        if ($image !== false) {
            imagesavealpha($image, true);
        }
        return $image;
    }

    /**
     * 处理图像背景
     * @param resource $image 图像资源
     * @param int $type 图像类型
     */
    protected function handleImageBackground($image, int $type): void
    {
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);
        } else {
            $white = imagecolorallocate($image, 255, 255, 255);
            imagefill($image, 0, 0, $white);
        }
    }

    /**
     * 保存图像
     * @param resource $image 图像资源
     * @param string $path 保存路径
     * @param int $type 图像类型
     * @param string $extension 文件扩展名
     * @return array 保存结果
     */
    protected function saveImage($image, string $path, int $type, string $extension): array
    {
        $result = ['success' => false, 'error' => ''];

        if ($type === IMAGETYPE_JPEG || $extension === '.jpg' || $extension === '.jpeg') {
            $quality = $this->compressConfig['quality'] ?? 85;
            $result['success'] = imagejpeg($image, $path, $quality);
            if ($result['success']) {
                $result['info'] = sprintf("JPEG质量: %d", $quality);
            }
        } elseif ($type === IMAGETYPE_PNG || $extension === '.png') {
            $pngQuality = min(9, max(0, $this->compressConfig['pngQuality'] ?? 9));
            $result['success'] = imagepng($image, $path, $pngQuality);
            if ($result['success']) {
                $result['info'] = sprintf("PNG压缩级别: %d", $pngQuality);
            }
        } elseif ($type === IMAGETYPE_GIF || $extension === '.gif') {
            $result['success'] = imagegif($image, $path);
        } elseif ($type === IMAGETYPE_WEBP && function_exists('imagewebp')) {
            $quality = $this->compressConfig['webpQuality'] ?? 80;
            $result['success'] = imagewebp($image, $path, $quality);
            if ($result['success']) {
                $result['info'] = sprintf("WebP质量: %d", $quality);
            }
        }

        return $result;
    }

    /**
     * 生成缩略图
     * @param string $sourcePath 源文件路径
     * @param string $relativePath 相对路径
     * @param string $extension 文件扩展名
     * @return array|null 缩略图信息或null
     */
    protected function generateThumbnail(string $sourcePath, string $relativePath, string $extension): ?array
    {
        if (!$this->thumbnailConfig['enabled']) {
            return null;
        }

        if (!in_array(strtolower($extension), $this->thumbnailConfig['types'], true)) {
            return null;
        }

        try {
            // 生成缩略图文件名
            $baseName = basename($relativePath);
            $thumbFileName = str_replace($extension, $this->thumbnailConfig['suffix'] . $extension, $baseName);
            $thumbPath = dirname($relativePath) . '/' . $thumbFileName;
            $thumbFullPath = $this->getFullPath($thumbPath);

            // 获取源图像信息
            $imageInfo = @getimagesize($sourcePath);
            if ($imageInfo === false) {
                return null;
            }

            [$width, $height, $type] = $imageInfo;

            // 创建源图像
            $source = $this->createImageResource($sourcePath, $type);
            if ($source === false) {
                return null;
            }

            // 计算缩略图尺寸
            $thumbWidth = $this->thumbnailConfig['width'] ?? 300;
            $thumbHeight = $this->thumbnailConfig['height'] ?? 200;

            // 计算等比例缩放
            $ratio = min($thumbWidth / $width, $thumbHeight / $height);
            $newWidth = (int) floor($width * $ratio);
            $newHeight = (int) floor($height * $ratio);

            // 创建缩略图画布
            $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
            if ($thumbnail === false) {
                imagedestroy($source);
                return null;
            }

            // 处理背景
            $this->handleThumbnailBackground($thumbnail, $type);

            // 计算居中位置
            $x = (int) floor(($thumbWidth - $newWidth) / 2);
            $y = (int) floor(($thumbHeight - $newHeight) / 2);

            // 复制并调整大小
            imagecopyresampled($thumbnail, $source, $x, $y, 0, 0, $newWidth, $newHeight, $width, $height);

            // 保存缩略图
            $saveResult = $this->saveThumbnail($thumbnail, $thumbFullPath, $type);

            // 清理资源
            imagedestroy($source);
            imagedestroy($thumbnail);

            if (!$saveResult) {
                return null;
            }

            return [
                'url' => $this->getUrl($thumbPath),
                'path' => $thumbPath,
                'width' => $newWidth,
                'height' => $newHeight,
            ];

        } catch (Exception $exception) {
            Yii::error('缩略图生成失败: ' . $exception->getMessage(), 'upload');
            return null;
        }
    }

    /**
     * 处理缩略图背景
     * @param resource $thumbnail 缩略图资源
     * @param int $type 图像类型
     */
    protected function handleThumbnailBackground($thumbnail, int $type): void
    {
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        } else {
            $white = imagecolorallocate($thumbnail, 255, 255, 255);
            imagefill($thumbnail, 0, 0, $white);
        }
    }

    /**
     * 保存缩略图
     * @param resource $thumbnail 缩略图资源
     * @param string $path 保存路径
     * @param int $type 图像类型
     * @return bool 是否成功
     */
    protected function saveThumbnail($thumbnail, string $path, int $type): bool
    {
        if ($type === IMAGETYPE_JPEG) {
            $quality = $this->thumbnailConfig['quality'] ?? 80;
            return imagejpeg($thumbnail, $path, $quality);
        } elseif ($type === IMAGETYPE_PNG) {
            return imagepng($thumbnail, $path, 9);
        } elseif ($type === IMAGETYPE_GIF) {
            return imagegif($thumbnail, $path);
        }

        return false;
    }

    /**
     * 生成随机名称
     * @param int $length 长度
     * @return string 随机名称
     */
    protected function generateRandomName(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }



    /**
     * base64编码的图片上传
     * 头像上传
     */
    public function actionUploadBase64Img()
    {
        $result = [];
        $base64Data = $_POST['img'];
        $img = base64_decode($base64Data);
        $file_exc   = ".jpg";//图片后缀
        $file_path      = $this->getImgPath();//图片路径
        $file_new_name  = Yii::$app->params['imagesUpload']['imgPrefix'].$this->random(10).$file_exc;//保存的图片名

        $filePath = Yii::getAlias("@attachment/").$file_path.$file_new_name;
        //移动文件
        if (!(file_put_contents($filePath, $img) && file_exists($filePath))) //移动失败
        {
            $result['flg'] = 2;
            $result['msg'] = "上传失败";
        }
        else //移动成功
        {
            $result['flg'] = 1;
            $result['msg'] = "上传成功";
            $result['imgName']  = $file_path.$file_new_name;//图片上传地址
            $result['imgPath']  = Yii::getAlias("@attachurl/").$file_path.$file_new_name;
            $result['url']  = Yii::getAlias("@attachurl/").$file_path.$file_new_name;
        }

        return json_encode($result);
    }

    /**
     * 上传文件方法
     */
    public function actionUploadVideos()
    {
//错误状态表
        $stateMap = Yii::$app->params['uploadState'];
        //默认返回状态
        $result = [];
        $result['flg'] = 2;
        $result['msg'] = $stateMap['ERROR_UNKNOWN'];

        $file = $_FILES['file'];
        if ($_FILES['file'])
        {
            $file_size  = $file['size'];//图片大小
            $file_name  = $file['name'];//图片原名称
            $file_exc   = $this->getFileExt($file_name);//图片后缀

            if($file_size > Yii::$app->params['fileUpload']['MaxSize'])//判定图片大小是否超出限制
            {
                $result['msg'] = $stateMap['ERROR_SIZE_EXCEED'];
            }
            else if(!$this->checkType2($file_exc))//检测类型
            {
                $result['msg'] = $stateMap['ERROR_TYPE_NOT_ALLOWED'];
            }
            else
            {
                //相对路径
                $filePath = $this->getImgPath().Yii::$app->params['imagesUpload']['imgPrefix'].$this->random(10).$file_exc;
                //上传
                $UploadFile     = UploadedFile::getInstanceByName('file');//利用yii2自带的上传图片
                $file_status    = $UploadFile->saveAs(Yii::getAlias("@attachment/").$filePath);//保存图片

                if($file_status == true)
                {
                    $result['flg'] = 1;
                    $result['msg'] = "上传成功";
                    $result['imgName']  = $filePath;//图片上传地址
                    $result['url']  = Yii::getAlias("@attachurl/").$filePath;
                    $result['state']  = 'success';
                    $result['type']=2;
                }
            }
        }

        return json_encode($result);
    }



    public function actionUploadFiles()
    {
//错误状态表
        $stateMap = Yii::$app->params['uploadState'];
        //默认返回状态
        $result = [];
        $result['flg'] = 2;
        $result['msg'] = $stateMap['ERROR_UNKNOWN'];

        $file = $_FILES['file'];
        if ($_FILES['file'])
        {
            $file_size  = $file['size'];//图片大小
            $file_name  = $file['name'];//图片原名称
            $file_exc   = $this->getFileExt($file_name);//图片后缀

            if($file_size > Yii::$app->params['fileUpload']['MaxSize'])//判定图片大小是否超出限制
            {
                $result['msg'] = $stateMap['ERROR_SIZE_EXCEED'];
            }
            else
            {
                //相对路径
                $filePath = $this->getImgPath().Yii::$app->params['imagesUpload']['imgPrefix'].$this->random(10).$file_exc;
                //上传
                $UploadFile     = UploadedFile::getInstanceByName('file');//利用yii2自带的上传图片
                $file_status    = $UploadFile->saveAs(Yii::getAlias("@attachment/").$filePath);//保存图片

                if($file_status == true)
                {
                    $result['flg'] = 1;
                    $result['msg'] = "上传成功";
                    $result['imgName']  = $filePath;//图片上传地址
                    $result['url']  = Yii::getAlias("@attachurl/").$filePath;
                    $result['state']  = 'success';
                    $result['type']=4;
                }
            }
        }

        return json_encode($result);
    }


    /**
     * @param null $Thumb 是否获取缩略图地址
     * @return string
     * 获取文件路径
     */
    public function getImgPath($thumb = null)
    {
        $file_path   = empty($thumb) ? Yii::$app->params['imagesUpload']['imgPath'] : Yii::$app->params['imagesUpload']['imgThumbPath'];//图片路径
        $ImgSubName  = Yii::$app->params['imagesUpload']['imgSubName'];//图片子路径
        $path    = $file_path.date($ImgSubName,time())."/";
        $addPath = Yii::getAlias("@attachment/").$path;
        $this->mkdirs($addPath);//创建路径

        return $path;
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    public function getFileExt($fileName)
    {
        return strtolower(strrchr($fileName, '.'));
    }

    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType($ext)
    {
        return in_array($ext, Yii::$app->params['imagesUpload']['imgMaxExc']);
    }



    private function checkType2($ext)
    {
        return in_array($ext, Yii::$app->params['fileUpload']['MaxExc']);
    }

    /**
     * 获取随机字符串
     */
    public function random($length, $numeric = FALSE)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));

        if ($numeric)
        {
            $hash = '';
        }
        else
        {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }

        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++)
        {
            $hash .= $seed[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * 检测目录并循环创建目录
     * @param $path
     */
    public function mkdirs($path)
    {
        if (!file_exists($path))
        {
            $this->mkdirs(dirname($path));
            mkdir($path, 0777);
        }
    }
}