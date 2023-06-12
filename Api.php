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

    public function getQuestion()
    {

        $marking_id = $this->validateParameter('marking_id', $this->param['marking_id'], STRING, false);


        $query = new Query;
        try {

            $resu = $query->get_single('marking', array('id' => $marking_id), 'id', 'desc');
            $results = $query->get_single('questions', array('id' => $resu->question_id), 'id', 'desc');

            if ($results) {
                $data = ['question' => $results];
                $this->returnResponse(SUCCESS_RESPONSE, $data);
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }

    public function updateOptions()
    {

        $marking_id = $this->validateParameter('marking_id', $this->param['marking_id'], STRING, false);
        $answer = $this->validateParameter('answer', $this->param['answer'], STRING, false);


        $query = new Query;
        try {

            $quer = $query->get_single('marking', array('id' => $marking_id), 'id', 'asc');
            $correct_opt = $quer->correct_option;
            $tt_id = $quer->test_taken_id;
            $results = $query->update('marking', 'id', $marking_id, array('selected_option' => $answer));

            if ($answer == $correct_opt) {
                $results = $query->correct_update($tt_id);
            } else {
                $results = $query->wrong_update($tt_id);
            }

            if ($results) {
                $data = ['questions' => $results];
                $this->returnResponse(SUCCESS_RESPONSE, $data);
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }
    public function getQuestions()
    {

        $tt_id = $this->validateParameter('tt_id', $this->param['tt_id'], STRING, false);
        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);


        $query = new Query;
        try {

            //$results = $query->get_multi('marking', array('test_taken_id' => $tt_id), 'id', 'asc');
            $results = $query->get_question_s($tt_id);

            if ($results) {
                $data = ['questions' => $results];
                $this->returnResponse(SUCCESS_RESPONSE, $data);
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }

    public function getResult()
    {

        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);
        $tt_id = $this->validateParameter('tt_id', $this->param['tt_id'], STRING, false);



        $query = new Query;
        try {

            $results = $query->get_single('takeTest', array('id' => $tt_id), 'id', 'asc');

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

    public function getTestDetails()
    {

        $test_id = $this->validateParameter('test_id', $this->param['test_id'], STRING, false);


        $query = new Query;
        try {

            $resul = $query->get_single('takeTest', array('id' => $test_id), 'id', 'asc');
            $results = $query->get_single('tests', array('id' => $resul->test_id), 'id', 'asc');

            if ($results) {
                $data = ['testDetail' => $results];
                $this->returnResponse(SUCCESS_RESPONSE, $data);
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }
    public function takeTest()
    {
        $test = $this->validateParameter('test', $this->param['test'], STRING, false);
        $year = $this->validateParameter('year', $this->param['year'], STRING, false);
        $subject = $this->validateParameter('subject', $this->param['subject'], STRING, false);
        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);


        $query = new Query;
        $num_question = $query->get_single('tests', array('id' => $test), 'id', 'asc')->num_question;

        $tt_id = $query->create('takeTest', array('student_id' => $student_id, 'test_id' => $test, 'year_id' => $year, 'subject_id' => $subject, 'num_question' => $num_question));


        $tests = $query->get_questions($year, $subject, $num_question);
        $no = 0;

        foreach ($tests as $test) {

            $no++;
            $save = $query->create('marking', array('question_id' => $test->id, 'test_taken_id' => $tt_id, 'correct_option' => $test->correct_option));
        }
        if ($no == $num_question) {
            $message = 'Test  Created Successfully';
            $this->returnResponse(SUCCESS_RESPONSE, $tt_id);
        } else {
            $message = 'Failed to Create Test';
            $this->returnResponse(FAILED_RESPONSE, $message);
        }
    }
    public function regSubject()
    {
        $subject_id = $this->validateParameter('subject_id', $this->param['subject_id'], STRING, false);
        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);



        $query = new Query;
        $check = $query->get_single('student_subjects', array('student_id' => $student_id, 'subject_id' => $subject_id), 'id', 'asc');

        if ($check) {
            if ($check->active == 0) {
                $tt_id = $query->update('student_subjects', 'id', $check->id, array('active' => 1));
            } else {
                $tt_id = $query->update('student_subjects', 'id', $check->id, array('active' => 0));
            }
        } else {
            $tt_id = $query->create('student_subjects', array('student_id' => $student_id,  'subject_id' => $subject_id));
        }




        if ($tt_id) {
            $message = 'Subject  Created Successfully';
            $this->returnResponse(SUCCESS_RESPONSE, $tt_id);
        } else {
            $message = 'Failed to Create Subject';
            $this->returnResponse(FAILED_RESPONSE, $message);
        }
    }

    public function checkSubject()
    {

        $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);


        $query = new Query;
        try {

            $resul = $query->get_count('student_subjects', array('student_id' => $student_id, 'active' => 1), 'id', 'desc');

            if ($resul >= 5) {
                $data = ['testDetail' => $resul];
                $this->returnResponse(SUCCESS_RESPONSE, $data);
            } else {
                $this->returnResponse(FAILED_RESPONSE, "Error Please Try Again.");
            }
        } catch (Exception $e) {
            $this->throwError(FAILED_RESPONSE, $e->getMessage());
        }
    }
    // public function setQuestions()
    // {
    //     $test = $this->validateParameter('test', $this->param['test'], STRING, false);
    //     $year = $this->validateParameter('year', $this->param['year'], STRING, false);
    //     $subject = $this->validateParameter('subject', $this->param['subject'], STRING, false);
    //     $student_id = $this->validateParameter('student_id', $this->param['student_id'], STRING, false);


    //     $query = new Query;
    //     $num_question = $query->get_single('tests', array('id' => $test), 'id', 'asc')->num_question;
    //     $tests = $query->get_questions($year, $subject, $num_question);
    //     $no = 0;
    //     $tt_id = $query->create('test_taken', array('subject_id' => $subject, 'test_id' => $test, 'year_id' => $year));
    //     foreach ($tests as $test) {

    //         $no++;
    //         $save = $query->create('marking', array('question_id' => $test->id, 'test_taken_id' => $tt_id));
    //     }
    //     if ($no === $num_question) {

    //         $message = 'Questions  Created Successfully';
    //         $this->returnResponse(SUCCESS_RESPONSE, 'success');
    //     } else {

    //         $message = 'Failed to Create Questions';
    //         $this->returnResponse(FAILED_RESPONSE, 'failed');
    //     }
    // }
}
