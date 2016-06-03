<?php
/**
 * Created by PhpStorm.
 * User: dev2
 * Date: 02/06/16
 * Time: 14:14
 */

namespace Ipedis;


class FileUpload
{



    //default settings
    public $destination = '/uploads/';
    public $fileName = 'file.txt';
    public $maxSize = '1048576'; // bytes (1048576 bytes = 1 meg)
    public $allowedExtensions = array('image/jpeg','image/png','text/plain', 'application/msword','application/pdf'); // mime types
    public $printError = TRUE;
    public $error = '';

    //START: Functions to Change Default Settings
    public function setDestination($newDestination) {
        $this->destination = $newDestination;
    }
    public function setFileName($newFileName) {
        $this->fileName = $newFileName;
    }
    public function setPrintError($newValue) {
        $this->printError = $newValue;
    }
    public function setMaxSize($newSize) {
        $this->maxSize = $newSize;
    }
    public function setAllowedExtensions($newExtensions) {
        if (is_array($newExtensions)) {
            $this->allowedExtensions = $newExtensions;
        }
        else {
            $this->allowedExtensions = array($newExtensions);
        }
    }
    //END: Functions to Change Default Settings

    //START: Process File Functions
    public function upload($file) {

        $this->validate($file);

        if ($this->error) {
            if ($this->printError) print $this->error;
        }
        else {
            move_uploaded_file($file['tmp_name'], $this->destination.$this->fileName) or $this->error .= 'Destination Directory Permission Problem.<br />';
            if ($this->error && $this->printError) print $this->error;
        }
    }
    public function delete($file) {

        if (file_exists($file)) {
            unlink($file) or $this->error .= 'Destination Directory Permission Problem.<br />';
        }
        else {
            $this->error .= 'File not found! Could not delete: '.$file.'<br />';
        }

        if ($this->error && $this->printError) print $this->error;
    }
    //END: Process File Functions

    //START: Helper Functions
    public function validate($file) {

        $error = '';

        //check file exist
        if (empty($file['name'][0])) $error .= 'No file found.<br />';
        //check allowed extensions
        if (!in_array($this->getExtension($file),$this->allowedExtensions)) $error .= 'Extension is not allowed.<br />';
        //check file size
        if ($file['size'][0] > $this->maxSize) $error .= 'Max File Size Exceeded. Limit: '.$this->maxSize.' bytes.<br />';

        $this->error = $error;
    }
    public function getExtension($file) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $ext = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        return $ext;
    }
    //END: Helper Functions


}