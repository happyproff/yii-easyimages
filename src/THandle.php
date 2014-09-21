<?php



namespace happyproff\YiiEasyImages;



trait THandle {



    protected function handleImages () {

        if (method_exists($this, 'images')) {
            foreach ($this->images() as $attribute => $config) {
                $image = CUploadedFile::getInstance($this, $attribute);
                try {
                    $imageName = Yii::app()->imageHandler->save($this, $attribute, $image, $config);

                    // delete old image
                    if ($this->$attribute and $data = json_decode($this->$attribute)) {
                        Yii::app()->imageHandler->delete($this, $attribute);
                    }

                    $this->$attribute = json_encode($imageName);
                } catch (\Exception $e) {
                    throw new CException('image saving failed');
                }
            }
        }

    }



} 