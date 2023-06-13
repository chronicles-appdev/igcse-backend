<?php

class  Query
{

    public $pdo;
    public function __construct()
    {
        $db = new DbConnect;
        $this->pdo = $db->connect();
    }



    public function checkInput($var)
    {
        $var = htmlspecialchars($var);
        $var = trim($var);
        $var = stripcslashes($var);
        return $var;
    }

    public function get_search_books($searchterm, $class_id)
    {

        $stmt = $this->pdo->prepare("SELECT * FROM upload_book  where book_title LIKE '%$searchterm%' AND class = '$class_id' ORDER BY `book_title` asc");
        $stmt->execute();
        $multi = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $multi;
    }
    public function get_question_s($tt_id)
    {

        // $stmt = $this->pdo->prepare("SELECT marking.id, questions.questionText, questions.optionA, questions.optionB, questions.optionC, questions.OptionD, questions.images FROM marking  inner join questions  ON  marking.question_id = questions.id where  marking.test_taken_id = $tt_id  ORDER BY marking.id ASC");

        //$stmt = $this->pdo->prepare("SELECT marking.id, questions.questionText, questions.optionA, questions.optionB, questions.optionC, questions.OptionD, questions.images FROM marking   INNER JOIN questions  ON  marking.question_id = questions.id where  marking.test_taken_id = $tt_id ");

        $stmt = $this->pdo->prepare("SELECT * FROM marking  JOIN questions  ON  marking.question_id = questions.id where  marking.test_taken_id = $tt_id ");


        $stmt->execute();
        $multi = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $multi;
    }

    public function get_questions($year_id, $subject_id, $num_question)
    {

        $stmt = $this->pdo->prepare("SELECT * FROM questions  where  subject_id = '$subject_id' AND q_year='$year_id' ORDER BY RAND() LIMIT $num_question");
        $stmt->execute();
        $multi = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $multi;
    }

    public function get_count($table, $fields = array(), $sort, $order)
    {
        $columns = '';
        $i       = 1;

        foreach ($fields as $name => $value) {
            $columns .= "`{$name}` = :{$name}";
            if ($i < count($fields)) {
                $columns .= ' AND ';
            }
            $i++;
        }
        $sql = "SELECT * FROM {$table}  WHERE {$columns} ORDER BY $sort $order";
        if ($stmt = $this->pdo->prepare($sql)) {
            foreach ($fields as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
            $count = $stmt->rowCount();
            // $single = $stmt->fetch(PDO::FETCH_OBJ);
        }
        return $count;
    }

    public function get_single($table, $fields = array(), $sort, $order)
    {
        $columns = '';
        $i       = 1;

        foreach ($fields as $name => $value) {
            $columns .= "`{$name}` = :{$name}";
            if ($i < count($fields)) {
                $columns .= ' AND ';
            }
            $i++;
        }
        $sql = "SELECT * FROM {$table}  WHERE {$columns} ORDER BY $sort $order";
        if ($stmt = $this->pdo->prepare($sql)) {
            foreach ($fields as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
            $single = $stmt->fetch(PDO::FETCH_OBJ);
        }
        return $single;
    }


    public function get_multi($table, $fields = array(), $sort, $order)
    {
        $columns = '';
        $i       = 1;

        foreach ($fields as $name => $value) {
            $columns .= "`{$name}` = :{$name}";
            if ($i < count($fields)) {
                $columns .= ' AND ';
            }
            $i++;
        }
        $sql = "SELECT * FROM {$table}  WHERE {$columns} ORDER BY $sort $order";
        if ($stmt = $this->pdo->prepare($sql)) {
            foreach ($fields as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
            $single = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        return $single;
    }


    public function get_All($table, $sort, $order)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM $table ORDER BY `$sort` $order");
        $stmt->execute();
        $multi = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $multi;
    }

    public function get_review($tt_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM marking inner join questions on marking.question_id=questions.id where test_taken_id=$tt_id;");
        $stmt->execute();
        $multi = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $multi;
    }



    public function get_std_student($id)
    {
        $stmt = $this->pdo->prepare("SELECT student_subjects.subject_id as id, subjects.subject_name FROM student_subjects inner join subjects ON student_subjects.subject_id = subjects.id where student_subjects.student_id = $id AND student_subjects.active = 1");
        $stmt->execute();
        $multi = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $multi;
    }




    public function login($table, $fields = array())
    {
        $columns = '';
        $i       = 1;

        foreach ($fields as $name => $value) {
            $columns .= "`{$name}` = :{$name}";
            if ($i < count($fields)) {
                $columns .= ' AND ';
            }
            $i++;
        }
        $sql = "SELECT * FROM {$table}  WHERE {$columns} ";
        if ($stmt = $this->pdo->prepare($sql)) {
            foreach ($fields as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_OBJ);
            $count = $stmt->rowCount();

            return $user;
        }
    }



    public function uploadImage($file)
    {
        $filename = basename($file['name']);
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $error = $file['error'];
        $original =  mt_rand(1111, 9999) . $filename;

        $ext = explode('.', $filename);
        //$ext = strtolower($ext);
        $allowed_ext = array('jpg', 'png', 'jpeg', 'JPG', 'PNG', 'JPEG');

        if (in_array($ext, $allowed_ext) === false) {
            if ($error === 0) {
                //  if($fileSize <= 209272152){
                $fileRoot = '../assets/images/' . $original;
                $fileRoots =  $original;
                move_uploaded_file($fileTmp, $fileRoot);
                return $fileRoots;

                // }
            }
        }
    }

    public function uploadDoc($file)
    {
        $filename = basename($file['name']);
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $error = $file['error'];
        $original = mt_rand(1111, 9999) . $filename;

        $ext = explode('.', $filename);
        //$ext = strtolower($ext);
        $allowed_ext = array('pdf', 'doc', 'docx', 'txt');

        if (in_array($ext, $allowed_ext) === false) {
            if ($error === 0) {
                //  if($fileSize <= 209272152){
                $fileRoot = '../assets/documents/' . $original;
                $fileRoots =  $original;
                move_uploaded_file($fileTmp, $fileRoot);
                return $fileRoots;

                // }
            }
        }
    }



    public function create($table, $fields = array())
    {
        $columns = implode(',', array_keys($fields));
        $values  = ':' . implode(', :', array_keys($fields));
        $sql     = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
        if ($stmt = $this->pdo->prepare($sql)) {
            foreach ($fields as $key => $data) {
                $stmt->bindValue(':' . $key, $data);
            }
            $stmt->execute();
            return $this->pdo->lastInsertId();
        }
    }


    public function update_act($days, $id, $student_id)
    {

        //$sql = "UPDATE 'activation_code' SET expiry_date = expiry_date + 43 WHERE id = 1";
        $sql = "UPDATE activation_code SET expiry_date = DATE_ADD(expiry_date, INTERVAL $days DAY), used = used + 1, student_id = $student_id WHERE id = $id";
        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }



    public function update_fisrt($id)
    {


        $sql = "UPDATE activation_code SET expiry_date = NOW() WHERE id = $id";
        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function update($table, $where, $id, $fields = array())
    {
        $columns = '';
        $i       = 1;

        foreach ($fields as $name => $value) {
            $columns .= "`{$name}` = :{$name}";
            if ($i < count($fields)) {
                $columns .= ', ';
            }
            $i++;
        }
        $sql = "UPDATE {$table} SET {$columns} WHERE {$where} = {$id}";
        if ($stmt = $this->pdo->prepare($sql)) {
            foreach ($fields as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            //var_dump($sql);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function correct_update($id)
    {


        $sql = "UPDATE takeTest SET  correctly_answ = correctly_answ + 1 WHERE id = $id";
        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function wrong_update($id)
    {


        $sql = "UPDATE takeTest SET  correctly_answ = wrongly_answ + 1 WHERE id = $id";
        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }



    public function delete($table, $array)
    {
        $sql = "DELETE FROM `{$table}`";
        $where = "WHERE ";
        foreach ($array as $name => $value) {
            $sql .= "{$where} `{$name}` = :{$name}";
            $where = " AND ";
        }
        if ($stmt = $this->pdo->prepare($sql)) {
            foreach ($array as $name => $value) {
                $stmt->bindvalue(':' . $name, $value);
            }
            $excex = $stmt->execute();
            if ($excex) {
                return true;
            }
        }
    }
}
