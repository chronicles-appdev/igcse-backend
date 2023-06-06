<?php

    require_once('constant.php');
    
    class Rest {

        protected $request;
        protected $serviceName;
        protected $param;

        public function __construct(){
            if($_SERVER['REQUEST_METHOD'] !== 'POST'){
                $this->throwError(REQUEST_METHOD_NOT_VALID, 'Request Method is not Allowed');
            }

            $handler = fopen('php://input', 'r');
            $this->request = stream_get_contents($handler);
            $this->validateRequest();

        }

        public function validateRequest(){
            if($_SERVER['CONTENT_TYPE'] !== 'application/json') {
                 $this->throwError(REQUEST_CONTENTTYPE_NOT_VALID, 'Request Content type is not Allowed');

            }
            $data = json_decode($this->request, true);
            
            if(!isset($data['name']) || $data['name'] == ""){
                $this->throwError(API_NAME_REQUIRED, "API name is required.");
            }
            $this->serviceName = $data['name'];

            if(!isset($data['param']) || $data['param'] == ""){
                $this->throwError(API_PARAM_REQUIRED, "API parameters are required.");
            }
            $this->param = $data['param'];

        }

        public function processApi(){
            $api = new API;
             if(!method_exists($api, $this->serviceName)){
                $this->throwError(API_DOES_NOT_EXIST, "API does not exist.");

            }
            $rMethod = new reflectionMethod('API', $this->serviceName);
           
            $rMethod->invoke($api);
            
        }

        public function throwError($code, $message){
            header("content-type: application/json");

            $errorMsg = json_encode(['response' => ['status' => $code, "result" => $message]]);
          
           // $errorMsg = json_encode(['error' => ['status' => $code, 'message' =>$message]]);
            echo $errorMsg; 
            exit;
        }

        public function validateParameter($fieldname, $value, $dataType, $required = true){
            if($required == true && empty($value) == true){
                $this->throwError(VALIDATE_PARAMETER_REQUIRED, $fieldname ." parameter is required");
            }
            switch($dataType){
                case BOOLEAN:
                    if(!is_bool($value)){
                        $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for ". $fieldname .'. It should be boolean.');
                    }
                break;

                case INTEGER:
                    if(!is_numeric($value)){
                        $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for ". $fieldname .'. It should be numeric.');
                    }
                break;

                case STRING:
                    if(!is_string($value)){
                        $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for ". $fieldname .'. It should be string.');
                    }
                break;
                default:
                if(!is_string($value)){
                    $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for ". $fieldname);
                }
                 break;

            }
            return $value;
        }
       
       
        public function returnResponse($code, $data){
            header("content-type: application/json");
            $response = json_encode(['response' => ['status' => $code, "result" => $data]]);
            echo $response; exit;
        }

        public function getAuthorizationHeader(){
            $headers = null;
            if(isset($_SERVER['Authorization'])){
                $headers = trim($_SERVER["Authorization"]);
            }
            elseif(isset($_SERVER['HTTP_AUTHORIZATION'])){
                $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
           elseif(function_exists('apache_request_headers')){
                $requestHeaders = apache_request_headers();
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                if(isset($requestHeaders['Authorization'])){
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
            return $headers;
        }

        public function getBearerToken(){
            $headers = $this->getAuthorizationHeader();
            if(!empty($headers)){
                if(preg_match('/Bearer\s(\S+)/', $headers, $matches)){
                    return $matches[1];
                }
            }
            $this->throwError(AUTHORIZATION_HEADER_NOT_FOUND, 'Access Token Not Found');
        }
       
       
       


    }




?>