<?php namespace Quasimodal\Uploadyoda;

class Helpers
{
    static function generateThumbnail($filename, $mime)
    {
        // check if image
        if (preg_match('/image/', $mime))
            return '<div class="thumb" style="background-image:url(/packages/quasimodal/uploadyoda/uploads/' . $filename . ');"/></div>';
        // check if pdf
        if (preg_match('/pdf/', $mime))
            return '<div class="preview-container"/><i class="fa fa-5x fa-file-text"></i></div>';
    }

}
