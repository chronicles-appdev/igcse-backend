<?php
   require_once('Rest.php');
    require_once('Query.php');
    
    require_once('DbConnect.php');
  

    class  Api extends Rest {
        
        public $dbConn;
        
        public function __construct(){
            parent::__construct();
            
            $db = new DbConnect;
            $this->dbConn = $db->connect();

            

        }
        
        // public function getAudios(){
        //     $token = $this->param['token'];
        //     $course_id = $this->validateParameter('course_id', $this->param['course_id'], STRING);
           
           
         
        //     $query = new Query;
        //     try {
        //         $token = $token;
        //         $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
        //         $student_id = $payload->userId;
        //         if(!empty($student_id)){
                    
        //             $courses = $query->get_audios($course_id);
        //             $data = ['audios' => $courses];
        //             $this->returnResponse(SUCCESS_RESPONSE, $data);           
                    
        //         }
               
        //     } catch (Exception $e){
        //         $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        //     }
        // }

        
        // public function getChats(){
        //          $token = $this->param['token'];
        //          $course_id = $this->validateParameter('course_id', $this->param['course_id'], STRING);
                
                
              
        //          $query = new Query;
        //          try {
        //              $token = $token;
        //              $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
        //              $student_id = $payload->userId;
        //              if(!empty($student_id)){
                         
        //                  $chats = $query->get_chats($student_id);
        //                  $data = ['chats' => $chats];
        //                  $this->returnResponse(SUCCESS_RESPONSE, $data);           
                         
        //              }
                    
        //          } catch (Exception $e){
        //              $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        //          }
        // }
     

        //      public function getVideos(){
        //         $token = $this->param['token'];
        //         $course_id = $this->validateParameter('course_id', $this->param['course_id'], STRING);
               
               
             
        //         $query = new Query;
        //         try {
        //             $token = $token;
        //             $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
        //             $student_id = $payload->userId;
        //             if(!empty($student_id)){
                        
        //                 $courses = $query->get_videos($course_id);
        //                 $data = ['videos' => $courses];
        //                 $this->returnResponse(SUCCESS_RESPONSE, $data);           
                        
        //             }
                   
        //         } catch (Exception $e){
        //             $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        //         }
        //     }
    
         

        // public function getCourseDetails() {
        //     $token = $this->param['token'];
        //     $course_id = $this->validateParameter('course_id', $this->param['course_id'], STRING);
           
           
         
        //     $query = new Query;
        //     try {
        //         $token = $token;
        //         $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
        //         $student_id = $payload->userId;
        //         if(!empty($student_id)){
                    
        //             $courses = $query->get_course_details($course_id);
        //             $data = ['course' => $courses];
        //             $this->returnResponse(SUCCESS_RESPONSE, $data);           
                    
        //         }
               
        //     } catch (Exception $e){
        //         $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        //     }
        // }

           
        // public function getUserCourses() {
        //     $token = $this->param['token'];
           
         
        //     $query = new Query;
        //     try {
        //         $token = $token;
        //         $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
        //         $student_id = $payload->userId;
        //         if(!empty($student_id)){
                    
        //             $courses = $query->getStudentCourses($student_id);
        //             $data = ['courses' => $courses];
        //             $this->returnResponse(SUCCESS_RESPONSE, $data);           
                    
        //         }
               
        //     } catch (Exception $e){
        //         $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        //     }
        // }

        

           
        // public function getUserResults() {
        //     $token = $this->param['token'];
           
         
        //     $query = new Query;
        //     try {
        //         $token = $token;
        //         $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
        //         $student_id = $payload->userId;
        //         if(!empty($student_id)){
                    
        //             $results = $query->getStudentResults($student_id);
        //             $data = ['results' => $results];
        //             $this->returnResponse(SUCCESS_RESPONSE, $data);           
                    
        //         }
               
        //     } catch (Exception $e){
        //         $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        //     }
        // }

        

           
        public function activate() {
            $act_code = $this->validateParameter('act_code', $this->param['act_code'], STRING, false);
            $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);
           
         
            $query = new Query;
            try {
                    
                    $results = $query->get_single('activation_code', array('activation_code' =>$act_code), 'id','desc');

                  if($results){

                    $data = ['results' => $results];
                    $this->returnResponse(SUCCESS_RESPONSE, $data); 

                  }else{
                    $this->returnResponse(FAILED_RESPONSE, "Invalid Activation Code.");
                  }
                             
                    
                
               
            } catch (Exception $e){
                $this->throwError(FAILED_RESPONSE, $e->getMessage());
            }
        }
     

           

        public function student() {
            $fullname = $this->validateParameter('fullname', $this->param['fullname'], STRING, false);
            $email = $this->validateParameter('email', $this->param['email'], STRING, false);
            $phone = $this->validateParameter('phone', $this->param['phone'], STRING, false);       
            $class = $this->validateParameter('class', $this->param['class'], STRING, false);
          
               
                $query = new Query;
                $student = $query->create('student', array('fullname'=>$fullname, 'class'=>$class, 'email'=>$email,  'phone'=>$phone));
                if($student){
                    $message = 'User Created Successfully';
                    $this->returnResponse(SUCCESS_RESPONSE, $student);
                }else{
                    $message = 'Failed to Create User';
                    $this->returnResponse(FAILED_RESPONSE, $message);
                }
           //     $this->returnResponse(SUCCESS_RESPONSE, $message);
           
        }
        public function school() {
            $school_name = $this->validateParameter('school_name', $this->param['schoolname'], STRING, false);
            $fullname = $this->validateParameter('fullname', $this->param['fullname'], STRING, false);
            $email = $this->validateParameter('email', $this->param['email'], STRING, false);
            $phone = $this->validateParameter('phone', $this->param['phone'], STRING, false);       
            $class = $this->validateParameter('class', $this->param['class'], STRING, false);
          
               
                $query = new Query;
                $student = $query->create('student', array('fullname'=>$fullname, 'class'=>$class, 'school_name'=>$school_name));
                if($student){
                   $school=  $query->create('school_reg', array('school_name'=>$school_name, 'student_id'=>$student, 'email'=>$email,  'phone'=>$phone));

                   if($school){
                    $message = 'User Created Successfully';
                    $this->returnResponse(SUCCESS_RESPONSE, $student);
                   }

                   
                }else{
                    $message = 'Failed to Create User';
                    $this->returnResponse(FAILED_RESPONSE, $message);
                }
               
           
        }


        // public function confirmLogin() {
        //     $token = $this->param['token'];
        //     $password = $this->param['password'];
           
         
        //     $query = new Query;
        //     try {
        //         $token = $token;
        //         $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
        //         $cust_id = $payload->userId;
        //         if(!empty($cust_id)){
                    
        //             $results = $query->get_single('customers', array('id' =>$cust_id), 'id','desc');
        //           if($results->password == MD5($password)){
        //             $this->returnResponse(SUCCESS_RESPONSE, 'Password is correct'); 
        //           }else{
        //             $this->returnResponse(INVALID_USER_PASS, "Password is incorrect.");
        //           }
                             
                    
        //         }
               
        //     } catch (Exception $e){
        //         $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        //     }
        // }
     

           
        // public function get_all_packages() {
        //     $token = $this->param['token'];
           
         
        //     $query = new Query;
        //     try {
        //         $token = $token;
        //         $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
        //         $cust_id = $payload->userId;
        //         if(!empty($cust_id)){
                    
        //             $results = $query->get_All('package', 'id', 'desc');
        //             $data = ['all_packages' => $results];
        //             $this->returnResponse(SUCCESS_RESPONSE, $data);           
                    
        //         }
               
        //     } catch (Exception $e){
        //         $this->throwError(ACCESS_TOKEN_ERRORS, $e->getMessage());
        //     }
        // }

        


        // public function generateToken(){
        //     $email = $this->validateParameter('email', $this->param['email'], STRING);
        //     $pass = $this->validateParameter('pass', $this->param['password'], STRING);
        //     $password =  MD5($pass);
        //   //  try {
        //         $stmt = $this->dbConn->prepare("SELECT * FROM `customers` WHERE email = :email AND password = :pass");
        //         $stmt->bindParam(":email", $email);
        //         $stmt->bindParam(":pass", $password);
        //         $stmt->execute();
        //         $user = $stmt->fetch(PDO::FETCH_OBJ);

              

        //         if($user){
        //             $payload = [
        //                 'iat' => time(),
        //                 'iss' => 'localhost',
        //                 'exp' => time() + (3600),
        //                  'is_customer' => 'yes',
        //                 'userId' => $user->id,
                        
        
        //             ];
        //             $token = JWT::encode($payload, SECRETE_KEY);
        //             $data = ['token' => $token];
        //             $this->returnResponse(SUCCESS_RESPONSE, $data);
          

        //         }else{
        //             $this->returnResponse(INVALID_USER_PASS, "Email or Password is incorrect.");
        //         }
                
                
           

        //     // } catch (Exception $e) {
        //     //     $this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
        //     // }
        // }


        
       


     



    }



?>