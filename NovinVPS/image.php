<?php


class image
{

    /**
     * image constructor.
     */
    public function __construct($images,$name_pdf)
    {
        //$images = array("file/test.jpg");
        $pdf = new Imagick($images);
        try{
            $pdf->setImageFormat('pdf');    
        } catch (Exception $e) {
            var_dump("setImageFormat faild in image.php file");
            die ("setImageFormat faild");
        }
        $pdf->writeImages($name_pdf, true);
    }
}