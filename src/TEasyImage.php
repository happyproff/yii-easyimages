<?php



namespace happyproff\YiiEasyImages;



trait TEasyImage {



    protected function handleImages () {

        if (method_exists($this, 'images')) {
            foreach ($this->images() as $attribute => $config) {
                $image = \CUploadedFile::getInstance($this, $attribute);
                try {
                    $imageName = \Yii::app()->easyimages->save($this, $attribute, $image, $config);

                    // delete old image
                    if ($this->$attribute and $data = json_decode($this->$attribute)) {
                        \Yii::app()->easyimages->delete($this, $attribute);
                    }

                    $this->$attribute = json_encode($imageName);
                } catch (\Exception $e) {
                    throw new \CException('image saving failed: "' . $e->getMessage() . '"');
                }
            }
        }

    }



    public function getImageUrl ($size = null, $attribute = null) {

        $sizes = $this->images();
        if (!$sizes) throw new \Exception('no images declaration');

        if ($attribute === null) {
            $attribute = key($sizes);
        }

        if (!$this->$attribute) return false;

        $url = '/images';
        $url .= '/' . strtolower(get_called_class());
        $url .= '/' . strtolower($attribute);

        $data = json_decode($this->$attribute, true);
        if (!$size) {
            if (!array_key_exists($attribute, $sizes)) return false;
            $size = key($sizes[$attribute]);
        }
        $url .= '/' . $data['id'] . '_' . $size . '.' . $data['ext'];

        return $url;

    }



} 