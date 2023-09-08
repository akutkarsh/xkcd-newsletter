<?php
namespace Akut;

use mysqli;

class Database
{
    private $host = "";
    private $user = "";
    private $password = "";
    private $database = "";
    private $con = null;

    private function initDB()
    {
        //using environmental variables
        $this->host = getenv("DB_HOST");
        $this->user = getenv("DB_ID");
        $this->password = getenv("DB_PASS");
        $this->database = getenv("DB_DB");

        if ($this->con != null) {
            return true;
        } else {
            try {
                $con = new \mysqli(
                    $this->host,
                    $this->user,
                    $this->password,
                    $this->database
                );
                $con->set_charset("utf8");
                $this->con = $con;
                return true;
            } catch (\Exception $e) {
                //do somethings like reconnection after sometime
                echo "lnot abe to connect";
                return false;
            }
        }
    }
    // call initDB in each of the following for cheking if the database is connected

    // addUser
    // - check whether user already exists; return true if it does
    // - otherwise write user, set status as unvalidated, create random key
    public function addUser($email)
    {
        if ($this->initDB() == false) {
            return false;
        }
        $res = $this->con->prepare(
            "select subStatus from mailingList where email = ?"
        );
        $res->bind_param("s", $email);
        $res->execute();
        $mysql_result = $res->get_result();

        $mysql_data = $mysql_result->fetch_all(MYSQLI_ASSOC);

        $check = $mysql_result->num_rows;

        if ($check >= 1) {
            return true;
        }

        // TO:DO use sha
        $token = random_bytes(32);
        $hash = bin2hex($token);
        $key = rawurlencode($hash);

        $res = $this->con->prepare(
            "insert into mailingList(email,subKey,createdAt,subStatus) values(?,?,now(),0)"
        );
        $res->bind_param("ss", $email, $key);
        $res->execute();
        if ($res->affected_rows > 0) {
            return true;
        } else {
            return false;
        }

        $res->close();
    }

    // removeUser
    // - removes users from table mailingList if he exists; return true if user removed
    public function removeUser($key)
    {
        if ($this->initDB() == false) {
            return false;
        }

        $res = $this->con->prepare("delete from mailingList where subKey = ? ");
        $res->bind_param("s", $key);
        $res->execute();
        $check = $res->affected_rows;
        $res->close();

        if ($check > 0) {
            return true;
        } else {
            return false;
        }
    }

    // checkSubscriptionStatus
    // - checks if users subscription is active ; return true if its active
    public function checkSubscriptionStatus($key)
    {
        if ($this->initDB() == false) {
            return false;
        }
        $res = $this->con->prepare(
            "select email, subStatus from mailingList where subKey = ?"
        );

        $res->bind_param("s", $key);
        $res->execute();

        $mysql_result = $res->get_result();
        $res->close();
        $mysql_data = $mysql_result->fetch_all(MYSQLI_ASSOC);

        $check = $mysql_result->num_rows;

        if ($check > 0) {
            $subStat = $mysql_data[0]["subStatus"];

            if ($subStat === 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    // checkSubscriptionStatus
    // - checks if users subscription is active ; return true if its active
    public function getSubscribedEmails(){
        if ($this->initDB() == false) {
            return false;
        }

        $res = $this->con->prepare(
            'select email,subKey from mailingList where subStatus = 1 ',
        );
        $res->execute();
        $mysql_result = $res->get_result();
        $mysql_data = $mysql_result->fetch_all(MYSQLI_ASSOC);
        $res->close();

        $check = $mysql_result->num_rows;

        if ($check > 0) {
            return $mysql_data;
        }else{
            return null;
        }
    }


    // getEmailusingValidationKey
    // returns emails in response to
    private function getEmailusingValidationKey($key)
    {

        if ($this->initDB() == false) {
            return false;
        }
        $res = $this->con->prepare(
            "select email from mailingList where subKey = ?"
        );

        $res->bind_param("s", $key);
        $res->execute();

        $mysql_result = $res->get_result();
        $res->close();
        $mysql_data = $mysql_result->fetch_all(MYSQLI_ASSOC);

        $check = $mysql_result->num_rows;
        if ($check > 0) {
            return $mysql_data[0]["email"];
        } else {
            return false;
        }
    }

    // checks url key with user key in database  ; return true if it matches
    public function validateUser($key)
    {
        if ($this->initDB() == false) {
            return false;
        }

        $res = $this->con->prepare(
            "update mailingList set subStatus = 1 where subKey = ?"
        );
        $res->bind_param("s", $key);
        $res->execute();
        $check = $res->affected_rows;
        $res->close();

        if ($check > 0) {
            return true;
        } else {
            return false;
        }
    }

    // get token using email ; return email if available
    public function getValidationKey($email)
    {
        if ($this->initDB() == false) {
            return "";
        }

        $res = $this->con->prepare(
            "select subKey from mailingList where email = ?"
        );
        $res->bind_param("s", $email);
        $res->execute();
        $mysql_result = $res->get_result();

        //test it out
        $mysql_data = $mysql_result->fetch_all(MYSQLI_ASSOC);
        $subKey = $mysql_data[0]["subKey"];
        return $subKey;
    }

    //logs cron success log to cronmaillog table in the database
    public function cronSuccess($email){

        if ($this->initDB() == false) {
            return false;
        }

            $started_at = date('Y-m-d H:i:s');
            $completed_at = date('Y-m-d H:i:s');

            $res = $this->con->prepare(
                'insert into cronmaillog(email,started_at,completed_at,is_done) values(?,now(),?,1)',
            );
            $res->bind_param('ss', $email, $completed_at);
            $res->execute();
            $res->close();
    }

    //logs cron failed log to cronmaillog table in the database
    public function cronFailed($email){

        if ($this->initDB() == false) {
            return false;
        }
            $res = $this->con->prepare(
                'insert into cronmaillog(email,started_at,completed_at,is_done) values(?,now(),?,0)',
            );
            $res->bind_param('ss', $email, $completed_at);
            $res->execute();
            $res->close();

    }
}
