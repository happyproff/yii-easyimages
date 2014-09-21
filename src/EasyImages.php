<?php



namespace happyproff\YiiEasyImages;



use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;



class EasyImages extends CApplicationComponent {



    const KEY_ID = 'id';
    const KEY_EXT = 'ext';



    /**
     * @var string
     */
    public $imagesBasePath = 'root.www.images';

    /**
     * @var Imagine\Gd\Imagine
     */
    public $processor;

    /**
     * @var string[]
     */
    protected $mimeToExtension = [
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
    ];



    public function init () {

        $this->processor = new Imagine\Gd\Imagine;

    }



    /**
     * @param CActiveRecord $model
     * @param string $attribute имя поля, содержащего CUploadedFile. В последствии этому полю будет присвоено имя файла изображения.
     * @param CUploadedFile $image
     * @param array $sizes
     *
     * @throws CException
     *
     * @return bool
     */
    public function save (CActiveRecord $model, $attribute = 'image', CUploadedFile $image, $sizes = []) {

        $folderModel = Yii::getPathOfAlias($this->imagesBasePath) . '/' . strtolower(get_class($model));
        if (!file_exists($folderModel)) mkdir($folderModel);

        $imageExtension = isset($this->mimeToExtension[$image->getType()]) ? $this->mimeToExtension[$image->getType()] : 'jpg';
        $imageId = $this->getRandomHash($model, $attribute);

        $imagePathTemp = Yii::getPathOfAlias('temp') . '/' . $imageId . '.' . $imageExtension;
        if ($image->saveAs($imagePathTemp)) {
            foreach ($sizes as $sizeName => $size) {
                $folderModelAttribute = $folderModel . '/' . strtolower($attribute);
                if (!file_exists($folderModelAttribute)) mkdir($folderModelAttribute);

                $pathImageSize = $folderModelAttribute . '/' . $imageId . '_' . $sizeName . '.' . $imageExtension;
                if (array_key_exists('enabled', $size) and $size['enabled'] == false) {
                    $this->processor
                        ->open($imagePathTemp)
                        ->save($pathImageSize, ['quality' => 90]);
                } else {
                    $this->processor
                        ->open($imagePathTemp)
                        ->thumbnail(
                                new Imagine\Image\Box($size['width'], $size['height']),
                                (!isset($size['inset']) or $size['inset'])
                                    ? Imagine\Image\ImageInterface::THUMBNAIL_INSET
                                    : Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND
                        )
                        ->save($pathImageSize, ['quality' => 90]);
                }
            }
        } else {
            throw new \CException('can not save image');
        }

        return [
            self::KEY_ID => $imageId,
            self::KEY_EXT => $imageExtension,
        ];

    }



    /**
     * @param CActiveRecord $model
     * @param string $attribute
     */
    public function delete (CActiveRecord $model, $attribute) {

        $path = $this->extractPath($model, $attribute);

        $data = json_decode($model->$attribute, true);
        if (
            $data
            and is_array($data)
            and isset($data[self::KEY_ID])
            and isset($data[self::KEY_EXT])
        ) {
            $files = (new Finder)->files()->in($path)->name($data[self::KEY_ID] . '*');
            (new Filesystem)->remove($files);
        }

    }



    /**
     * @param CActiveRecord $model
     * @param string $attribute
     *
     * @return string
     */
    protected function extractPath (CActiveRecord $model, $attribute) {

        $path = Yii::getPathOfAlias($this->imagesBasePath) . '/' . strtolower(get_class($model)) . '/' . strtolower($attribute);

        return $path;

    }



    /**
     * @param object $model
     * @param string $attribute
     * @return string
     */
    protected function getRandomHash ($model, $attribute) {

        return md5(uniqid(get_class($model) . $attribute));

    }



}
 