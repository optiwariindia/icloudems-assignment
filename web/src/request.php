<?php
namespace icloudems\assignment;
class request extends \optiwariindia\website\request{
    public static function inputs(){
        $inp=parent::inputs();
        if(count($_FILES)){
            $inp=array_merge($inp,$_FILES);
        }
        return $inp;
    }
}