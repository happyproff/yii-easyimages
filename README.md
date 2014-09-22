yii-easyimages
==============

[![License](https://poser.pugx.org/happyproff/yii-easyimages/license.svg)](https://packagist.org/packages/happyproff/yii-easyimages)

Простая работа с изображениями для моделей. Автоматическая загрузка, генерация нужных размеров, удаление.

## Использование

Добавить в `composer.json` зависимость:

```json
"happyproff/yii-easyimages": "*@dev"
```

Зарегистрировать компонент приложения:

```php
'components' => [
    ...,
    'easyimages' => [
        'class' => 'happyproff\YiiEasyImages\EasyImages',
    ],
    ...
]
```

В базовом AR классе или в конкретной модели использовать трейт и вызвать его метод в beforeSave():

```php
class MCategory extends ActiveRecord {
    use happyproff\YiiEasyImages\TEasyImage;
    ...
```

```php
public function beforeSave () {
    if (!parent::beforeSave()) return false;

    if (method_exists($this, 'handleImages')) {
        $this->handleImages();
    }

    return true;
}
```

Определить атрибуты, которые будут использоваться для работы с изображениями и их пресеты:

```php
public function images () {
    return [
        'image' => [
            self::IMAGE_ORIGINAL = ['width' => 1920, 'height' => 1080, 'enabled' = false],
            self::IMAGE_FULL => ['width' => 960, 'height' => 720, 'quality' => 100],
            self::IMAGE_LIST => ['width' => 146, 'height' => 160, 'inset' => false,],
            self::IMAGE_ITEM => ['width' => 300, 'height' => 99'],
        ],
    ];
}
```
