<?php



namespace happyproff\YiiEasyImages;



use Yii;
use Html;



trait TActiveFormImageField {



    static $assetsRegistered;



    public function imageFieldControlGroup (\CActiveRecord $model, $attribute, $htmlOptions = [], $size = null) {

        $htmlOptions = $this->processControlGroupOptions($model, $attribute, $htmlOptions);

        $input = '';
        $deleteCheckbox = '';
        if ($model->$attribute and $imageUrl = $model->getImageUrl($size)) {
            $input .= Html::image($imageUrl) . '<br>';
            $deleteCheckbox = Html::checkBox(get_class($model) . '[delete_images][' . $attribute . ']', false, ['label' => 'Удалить это изображение?']);
        }
        $input .= Html::activeFileField($model, $attribute);
        $input .= $deleteCheckbox;

        $htmlOptions['input'] = $input;

        $html = Html::activeControlGroup(Html::INPUT_TYPE_FILE, $model, $attribute, $htmlOptions, []);

        return $html;

    }



}
