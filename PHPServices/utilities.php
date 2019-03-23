<?php
    //Snippet from PHP Share: http://www.phpshare.org
    function formatSizeUnits($bytes){
        if ($bytes >= 1000000000){
            $bytes = number_format($bytes / 1000000000, 2) . ' GB';
        }
        elseif ($bytes >= 1000000){
            $bytes = number_format($bytes / 1000000, 2) . ' MB';
        }
        elseif ($bytes >= 1000){
            $bytes = number_format($bytes / 1000, 2) . ' KB';
        }
        elseif ($bytes > 1){
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1){
            $bytes = $bytes . ' byte';
        }
        else{
            $bytes = '0 bytes';
        }
        return $bytes;
    }
?>