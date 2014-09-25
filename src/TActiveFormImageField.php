<?php



namespace happyproff\YiiEasyImages;



use Yii;
use Html;



trait TActiveFormImageField {



    static $assetsRegistered;



    public function imageFieldControlGroup (\CActiveRecord $model, $attribute, $htmlOptions = [], $size = null) {

        $htmlOptions = $this->processControlGroupOptions($model, $attribute, $htmlOptions);

        $input = '';
        if ($model->$attribute and $imageUrl = $model->getImageUrl($size)) {
            $input .= Html::image($imageUrl) . '<br>';
        }
        $input .= Html::activeFileField($model, $attribute);
        $htmlOptions['input'] = $input;

        $html = Html::activeControlGroup(Html::INPUT_TYPE_FILE, $model, $attribute, $htmlOptions, []);

        return $html;

    }



}
