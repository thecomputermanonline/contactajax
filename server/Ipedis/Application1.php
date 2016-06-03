<?php
/**
 * File: Application.php
 * User: mofasa
 * Date: 26/02/15 14:19
 */

namespace Ipedis;
use Ipedis;

class Application{
    const ENV = "dev";
    protected $currentLang = "en"; //default lang
    protected static $_instance = null;
    public static function getInstance(){
        if(empty(self::$_instance)){
            $singletonClass = get_called_class(); //Determine children class !
            self::$_instance = new $singletonClass();
        }
        return self::$_instance;
    }
    public function run(){
        //First step is to send json header.
        if(self::ENV !== "dev")
            header('Content-type: application/json');
        //First Step, check if it's ajax request
        if(self::ENV !== "dev")
            $this->checkAjaxRequest();

        $this->setupParameters();
        //Check current Lang
        //if parameter get lang exist, real string and lenght it equal to 2.
        if(!empty($_GET["lang"]) && is_string($_GET["lang"]) && strlen($_GET["lang"]) == 2)
            $this->currentLang = $_GET["lang"];

//        $fileuploadresult1 =  $this->uploadFile( $_GET["photo1"], $_GET["photo1"]["name"], "image/jpeg");
//
//        $fileuploadresult2 =  $this->uploadFile( $_GET["photo2"], $_GET["photo2"]["name"], "image/jpeg");
//
//        $fileuploadresult3 =  $this->uploadFile( $_GET["resume"], $_GET["resume"]["name"], "application/pdf");
        ///Get error Message if they are.
        //var_dump($fileuploadresult);exit;

       // $fileerrorMessage = $this->processFiles($_FILES);
        $errorMessage = $this->checkParams($_GET);

        if(!empty($errorMessage) || ( $fileuploadresult1 != null) || ( $fileuploadresult2 != null)|| ( $fileuploadresult3 != null) )  {
            self::getHeaderStatutCode(400); //Missing paraméter
            $retour = array(
                "code" => 400,
                "message" => $errorMessage,
                "previousdata" => $_GET,
                "status" => "error",
                "errors" => $errorMessage. " and ".$fileuploadresult1 . "and ". $fileuploadresult2 . "and ". $fileuploadresult3,
                "fileerror"=> $fileuploadresult1 ."and ". $fileuploadresult2 . "and ". $fileuploadresult3
            );
        }
        else{

           $samplefile1 = $_SERVER["DOCUMENT_ROOT"] . "/contactajax/uploads/".$_GET["photo1"]["name"];
            $samplefile2 = $_SERVER["DOCUMENT_ROOT"] . "/contactajax/uploads/".$_GET["photo2"]["name"];
            $samplefile3 = $_SERVER["DOCUMENT_ROOT"] . "/contactajax/uploads/".$_GET["resume"]["name"];
            //We can send email.
         // var_dump(  $samplefile);exit;

            $mailBuilder = new \Ipedis\Email();
            $mail = $mailBuilder->mailFactory();
            $mail = $mailBuilder->configureMail($mail);
            $mail->From    = $_GET["email"];
            $mail->Subject = 'Octopouce site - Form contact - '.htmlentities($_GET["name"]); // Set subject
            $mail->Body    = nl2br($mailBuilder->buildMessage($_GET)); // Construct message for html version
            $mail->altBody = strip_tags($mailBuilder->buildMessage($_GET));
            $mail->addAttachment($samplefile1);
            $mail->addAttachment($samplefile2);
            $mail->addAttachment($samplefile3);

//            if (isset($_GET["uploaded_resume"])){
//                $mail->AddAttachment($_FILES['uploaded_file']['tmp_name'], $_FILES['uploaded_file']['name']);
//            }
//            if (isset($_GET["uploaded_photo1"])){
//                $mail->AddAttachment($_FILES['uploaded_file']['tmp_name'], $_FILES['uploaded_file']['name']);
//            }
//            if (isset($_GET["uploaded_photo2"])){
//                $mail->AddAttachment($_FILES['uploaded_file']['tmp_name'], $_FILES['uploaded_file']['name']);
//            }
            $mail->send();
         //$path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment')
            $retour = array(
                "code" => 201,
                "message" => $this->translate("message_send"),
                "status" => "success",
                 "success" => "success"
            );
        }
        //Show feedback for front
       echo json_encode($retour);
        //return true;
//        $response_array['status'] = 'success'; /* match error string in jquery if/else */
//        $response_array['message'] = 'RFQ Sent!';   /* add custom message */
//        header('Content-type: application/json');
//        echo json_encode($response_array);


    }

    /**
     * @param $params
     * // name
     * // email
     * // cell number
     * // message
     */
    protected function checkParams($params){
        $keyparams = ["name","sujet","phone", "message","email" ,"photo1" ,"photo2" ,"resume"];
       // $keyparams = ["name","phone", "email"];
        $message = [];
        foreach($keyparams as $key){

            if(empty($params[$key]))
                $message[$key] = sprintf($this->translate("missing_parameter"),$key);
        }
        if(!empty($params["email"]) && !$this->isMailPattern($params["email"])){
            $message["email"] = $this->translate("wrong_email");
        }
        if(!empty($params["phone"]) && !$this->isValidPhoneNumber($params["phone"])){
            $message["phone"] = ""; //TODO : complete error message.
        }
        if(!empty($params["photo1"]) && !$this->uploadFile( $_GET["photo1"], $_GET["photo1"]["name"], "image/jpeg")){
            $message["phone"] = ""; //TODO : complete error message.
        }
        if(!empty($params["photo2"]) && !$this->isValidPhoneNumber($params["phone"])){
            $message["phone"] = ""; //TODO : complete error message.
        }
        if(!empty($params["resume"]) && !$this->isValidPhoneNumber($params["phone"])){
            $message["phone"] = ""; //TODO : complete error message.
        }
        return $message;
    }


//    protected function processFiles($files){
//
//        $file_message = [];
//
//        //    =  $this->uploadFile( $_GET["photo1"], $_GET["photo1"]["name"], "image/jpeg");
//       // $keyparams = ["name","sujet","phone", "message","email" ,"photo1" ,"photo2" ,"resume"];
//        // $keyparams = ["name","phone", "email"];
//        $filemessage = [];
//        foreach($files as $key){
//
//            $file_message[$key] = $this->uploadFile( $files[0], $files[0]["name"], "image/jpeg");
//
//            if(empty($params[$key]))
//                $message[$key] = sprintf($this->translate("missing_parameter"),$key);
//        }
//        if(!empty($params["email"]) && !$this->isMailPattern($params["email"])){
//            $message["email"] = $this->translate("wrong_email");
//        }
//        if(!empty($params["phone"]) && !$this->isValidPhoneNumber($params["phone"])){
//            $message["phone"] = ""; //TODO : complete error message.
//        }
//
//
//        $fileuploadresult1 =  $this->uploadFile( $_GET["photo1"], $_GET["photo1"]["name"], "image/jpeg");
//
//        $fileuploadresult2 =  $this->uploadFile( $_GET["photo2"], $_GET["photo2"]["name"], "image/jpeg");
//
//        $fileuploadresult3 =  $this->uploadFile( $_GET["resume"], $_GET["resume"]["name"], "application/pdf");
//
//        return $file_message;
//    }


    //PRAGMA PART : Email Validation
    protected function isMailPattern($email){
        return preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/",$email);
    }

    protected function isValidPhoneNumber($phone){
        return true; //TODO : write regex.
    }

    //PRAGMA PART : Tools
    protected function translate($key){
        return Translator::getInstance()
            ->translate($key,$this->currentLang);
    }
    protected function checkAjaxRequest(){
        if(!$this->isAjaxRequest())
            throw new RouterException("Direct Access is forbidden", 403);
    }

    protected function isAjaxRequest(){
        return (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    protected function setupParameters(){
        $params = [];
        //var_dump($_FILES);exit;
        $_GET = array_merge($_GET,$_POST,$_FILES);
        //var_dump($_GET);exit;
        $input = file_get_contents("php://input");

        if(!empty($input))
            $_GET = array_merge($_GET,json_decode(file_get_contents("php://input"),true));


    }

    //PRAGMA PART : Manage Error / Exeption
    public function factoryError(\Ipedis\RouterException $e){
        self::getHeaderStatutCode($e->getCode());
        echo json_encode(array(
            "type" => "error",
            "message" => $e->getMessage(),
            "code" => $e->getCode()
        ));
    }

    /**
     * @param $httpCode
     * @description HTTP Error factory
     * @return string
     */
    public static function getHeaderStatutCode($httpCode) {
        $status_codes = array(100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 307 => 'Temporary Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 426 => 'Upgrade Required', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 509 => 'Bandwidth Limit Exceeded', 510 => 'Not Extended');
        if (empty($status_codes[$httpCode]))// Unknown error ?
            $httpCode = 500;

        $status_string = $httpCode . ' ' . $status_codes[$httpCode];
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_string, true, $httpCode);
        return $status_string;
    }


public static function uploadFile($file, $filename, $filetype){
$imgUploader = new \Ipedis\FileUpload();
$imgUploader->setPrintError(FALSE);

//store errors
$errors = '';

$imgUploader->setDestination ($_SERVER['DOCUMENT_ROOT'] . '/contactajax/uploads/');
    //$imgUploader->setDestination($_SERVER['DOCUMENT_ROOT'].'/jobscout/');
$imgUploader->setAllowedExtensions($filetype);
$imgUploader->setFileName($filename); //TODO : generate new unique name each time;
$imgUploader->upload($file);
$errors .= $imgUploader->error;

//$imgUploader->setDestination($_SERVER['DOCUMENT_ROOT'] . '/images/thumbs/');
//$imgUploader->setAllowedExtensions(array('image/jpg','image/gif','image/png'));
//$imgUploader->setFileName($_FILES['thumbnailimage']['tmp_name'][0]);
//$imgUploader->upload($_FILES['thumbnailimage']);
//$errors .= $imgUploader->error;

return  $errors;
}

}
