<?php
require_once('Rest.php');
require_once('Query.php');

require_once('DbConnect.php');


class  Api extends Rest
{

    public $dbConn;

    public function __construct()
    {
        parent::__construct();

        $db = new DbConnect;
        $this->dbConn = $db->connect();
    }
    public function confirmPay()
    {
        $ref = $this->validateParameter('ref', $this->param['ref'], STRING, false);
        $user_id = $this->validateParameter('user_id', $this->param['user_id'], INTEGER, false);
        $plan = $this->validateParameter('plan', $this->param['plan'], STRING, false);
        $amount = $this->validateParameter('amount', $this->param['amount'], INTEGER, false);
        $duration = $this->validateParameter('duration', $this->param['duration'], INTEGER, false);




        $query = new Query;
        try {

            $url = 'https://api.paystack.co/transaction/verify/' . $ref;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: Bearer sk_live_89e8caf2dc5bea4def7b8c526e0a0c316bf87e12'
                ]
            );
            $request = curl_exec($ch);
            curl_close($ch);

            if ($request) {
                $result = json_decode($request, true);
            }

            // $output = array('success' => 0);


            if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success')) {


                if ($plan == 'term') {
                    $amount = 100;
                } elseif ($plan == 'month') {
                    $amount = 100;
                } elseif ($plan == 'year') {
                    $amount = 100;
                } else {
                    $this->returnResponse(FAILED_RESPONSE, "Invalid Subscription Plan.");
                }
                if ($result['data']['amount'] >= $amount) {

                    $student = $query->create('payment', array('plan' => $plan, 'user_id' => $user_id,  'amount' => $amount, 'ref' => $ref, 'duration' => $duration,));
                    if ($student) {
                        $message = 'Payment Successful';
                        $this->returnResponse(SUCCESS_RESPONSE, $student);
                    } else {
                        $message = "Failed to Create Payment Details";
                        $this->returnResponse(FAILED_RESPONSE, $message);
                    }
                } else {
                    $message = "Invalid Amount Paid";
                    $this->returnResponse(FAILED_RESPONSE, $message);
                }
            } else {
                $message = "Payment Failed Try again Later";
                $this->returnResponse(FAILED_RESPONSE, $message);
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }
    public function getSearchBooks()
    {
        $class_id = $this->validateParameter('class', $this->param['class'], STRING, false);
        $searchTerm = $this->validateParameter('searchTerm', $this->param['searchTerm'], STRING, false);


        $query = new Query;
        try {

            $results = $query->get_search_books($searchTerm, $class_id);

            if ($results) {
                $data = ['books' => $results];
                $this->returnResponse(SUCCESS_RESPONSE, $data);
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }
    public function getTests()
    {

        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);


        $query = new Query;
        try {

            $results = $query->get_All('tests', 'test_name', 'asc');

            if ($results) {
                $data = ['tests' => $results];
                $this->returnResponse(SUCCESS_RESPONSE, $data);
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }
    public function getYears()
    {

        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);


        $query = new Query;
        try {

            $results = $query->get_All('years', 'year_name', 'desc');

            if ($results) {
                $data = ['years' => $results];
                $this->returnResponse(SUCCESS_RESPONSE, $data);
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }
    public function getSubjects()
    {

        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);


        $query = new Query;
        try {

            $results = $query->get_All('subjects', 'subject_name', 'asc');

            if ($results) {
                $data = ['subjects' => $results];
                $this->returnResponse(SUCCESS_RESPONSE, $data);
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }
    public function activate()
    {
        $act_code = $this->validateParameter('act_code', $this->param['act_code'], STRING, false);
        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);


        $query = new Query;
        try {

            $results = $query->get_single('activation_code', array('activation_code' => $act_code), 'id', 'desc');

            if ($results) {
                $days = $results->months * 30;



                if (($results->used) < ($results->num_user)) {

                    $first = $query->update_fisrt($results->id);
                    if ($first) {
                        $edit =  $query->update_act($days, $results->id, $student_id);
                    }


                    //$edit =  $query->update_act($days, $results->id, $student_id);

                    if ($edit) {
                        $data = ['results' => $results];
                        $this->returnResponse(SUCCESS_RESPONSE, $data);
                    } else {
                        $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
                    }
                } else {
                    $this->returnResponse(FAILED_RESPONSE, "This Activation Code has been Used.");
                }
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Invalid Activation Code.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }
    public function student()
    {
        $fullname = $this->validateParameter('fullname', $this->param['fullname'], STRING, false);
        $email = $this->validateParameter('email', $this->param['email'], STRING, false);
        $phone = $this->validateParameter('phone', $this->param['phone'], STRING, false);
        $class = $this->validateParameter('class', $this->param['class'], STRING, false);


        $query = new Query;
        $student = $query->create('student', array('fullname' => $fullname, 'email' => $email, 'class' => $class, 'phone' => $phone));
        if ($student) {
            $message = 'User Created Successfully';
            $this->returnResponse(SUCCESS_RESPONSE, $student);
        } else {
            $message = 'Failed to Create User';
            $this->returnResponse(FAILED_RESPONSE, $message);
        }
        //     $this->returnResponse(SUCCESS_RESPONSE, $message);

    }
    public function takeTest()
    {
        $test = $this->validateParameter('test', $this->param['test'], STRING, false);
        $year = $this->validateParameter('year', $this->param['year'], STRING, false);
        $subject = $this->validateParameter('subject', $this->param['subject'], STRING, false);
        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);


        $query = new Query;
        $tests = $query->create('takeTest', array('student_id' => $student_id, 'test_id' => $test, 'year_id' => $year, 'subject_id' => $subject));
        if ($tests) {
            $message = 'Test  Created Successfully';
            $this->returnResponse(SUCCESS_RESPONSE, $tests);
        } else {
            $message = 'Failed to Create Test';
            $this->returnResponse(FAILED_RESPONSE, $message);
        }
        //     $this->returnResponse(SUCCESS_RESPONSE, $message);

    }
}
