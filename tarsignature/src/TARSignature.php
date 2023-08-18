<?php

namespace GlpiPlugin\TARSignature;

use CommonDBTM;

class TARSignature extends CommonDBTM
{

    // Should return the localized name of the type
    static function getTypeName($nb = 0)
    {
        $text = '';
        switch ($nb) {
            case 1:
                $text = 'Assinatura'; 
                break;
            
            default:
            $text = 'TARSignature'; 
                break;
        }
        return $text;
    }
    /**
     * @see CommonGLPI::getMenuName()
     **/
    static function getMenuName()
    {
        return __('TAR Signature');
    }
 
}