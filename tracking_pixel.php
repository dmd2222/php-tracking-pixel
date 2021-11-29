<?php

/*
Copyright (c) CS-Digital UG (hatungsbeschränkt) https://cs-digital-ug.de/ 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/*Version: 1.0.2.1 First Edition */


//options
$debugging_switch=false; //Switch on debugging. ATTENTION: do only switch one if you need it.
$json_log_switch = false; //Write text as json in the log.
$delete_logs_after = Array(1,10); //Delete Logs (0/1) ->no/yes after (x) -> days. Ex. Array(1,10) -> yes after 10 days.
$send_data_as_post_request=Array("0","https://cs-digital-ug.de/data_donation.php"); // Send Data by script to post request. 0/1 -> no/yes -- url endpoint



//credits in email
$credits_in_email = true; //only one more line in email: "Script written by ...."

//statistics / data donation
$full_data_donation = true; // true = send full data donation -- false = send only using one time ping. (No more data then, ip used it once)


//Report error messages to visitor
//error_reporting(0); //report php errors -1 / do not report error messages 0
error_reporting(E_ERROR | E_PARSE); // Show errors but no warnings.


//preperations
    //log cleaning?
    if($delete_logs_after[0] ==1){
        
        //Check and create folder
        $path = "log";
        if (!is_dir($path)) {
            mkdir($path, 0700, true);
        }
        delete_old_files("log", "*", $delete_logs_after[1]*86400);
    }










// Get values
$get_key="em";
if(isset($_GET[$get_key]) && !empty($_GET[$get_key]))
{
    $email_base64 = xss_clean($_GET[$get_key]) ;
}else{
    $email_base64 = "";
}


$get_key="ri";
if(isset($_GET[$get_key]) && !empty($_GET[$get_key]))
{
    $replace_image = xss_clean($_GET[$get_key]) ;
}else{
    $replace_image = "";
}

$get_key="re";
if(isset($_GET[$get_key]) && !empty($_GET[$get_key]))
{
    $redirect_url = xss_clean($_GET[$get_key]) ;
}else{
    $redirect_url = "";
}


$get_key="ai";
if(isset($_GET[$get_key]) && !empty($_GET[$get_key]))
{
    $additional_info = xss_clean($_GET[$get_key]) ;
}else{
    $additional_info = "";
}



$get_key="id";
if(isset($_GET[$get_key]) && !empty($_GET[$get_key]))
{
    $id_key = xss_clean($_GET[$get_key]) ;
}else{
    $id_key = "";
}



//Debugging
if($debugging_switch == true){
    echo"GETs:";
    print_r($_GET);
    echo"All vars:" ;
    print_r(array_keys(get_defined_vars()));
}


//#########################################
    //check if it is a id_key read request
            $get_key="getid";
            if(isset($_GET[$get_key]) && !empty($_GET[$get_key]))
            {
              
                $id_key_getid = xss_clean($_GET[$get_key]) ;

                //search for id
                $file_name = "id_" . $id_key_getid . ".txt";
                if(file_exists("ids/". $file_name)){
                        //found
                        echo  file_get_contents("ids/" . $file_name);
                        die();
                }else{
                        //not found
                        echo json_encode(Array("status",$id_key_getid,"not found"));
                        die();
                }

            }
 
//#########################################



//Convert values
if($email_base64 != ""){
    $email_clear = xss_clean(base64_decode($email_base64)) ;
}




//Debugging
if($debugging_switch == true){
    echo "email_base64:" .  $email_base64;
    echo "email_clear:" .  $email_clear;
}


// Check values
// Check email address
if($email_clear != ""){
    if (!filter_var($email_clear, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        throw new Exception('Email is not valid.');
      }
}


//Check replace image
if($replace_image != ""){
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$replace_image)) {
            http_response_code(400);
            throw new Exception('Replace image is not valid.');
        }
}

  //Check redirect url
  if($redirect_url != ""){
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$redirect_url)) {
            http_response_code(400);
            throw new Exception('Replace image is not valid.');
        }
}

  //Check additional info
  if($additional_info != ""){
        if(preg_match("/[^a-zA-Z0-9]+/",$additional_info)==False){
            $additional_info = preg_replace("/[^a-zA-Z0-9]+/", "", $additional_info);
        }
}


  //Check id number
  if($id_key != ""){
        if((!is_numeric($id_key)) ){
            http_response_code(400);
            throw new Exception('Id key is not valid.');
        }
  }







  //main program
//##########################################################
  //send email and/or logfile

    //generate all server vars
    $all_server_vars ="";
    foreach($_SERVER AS $key => $value)
      {
          $all_server_vars .= $key.": ".$value . "<br>".PHP_EOL;
      }


      //Debugging
    if($debugging_switch == true){
        echo " all_server_vars:" .   $all_server_vars;
    }



    $subject="";
    $text="";

    //add all server vars
    $text .= "all_server_vars:". $all_server_vars;
    //add all server vars
    $text .= "additional_info:". $additional_info;


    
      //Debugging
      if($debugging_switch == true){
        echo " text:" .   $text;
        }





  if($email_clear != ""){

        // send email
        send_email($email_clear, $subject, $text);

              //Write in logfile
      write_log($subject . ":" . $text);


  }else{
        //Write in logfile
        write_log($subject . ":" . $text);
  }





  //send post request, if needed
  //###################################
  if($send_data_as_post_request[0]==1){

        $end_url = $send_data_as_post_request[1];

        //check endpoint url
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $end_url)) {
            http_response_code(400);
            throw new Exception('Replace image is not valid.');
        }
        write_log(send_post_request($end_url,Array($text)));

  }




  //send data donation
  


    //data donation
    if($full_data_donation == false){

                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }

                $end_url='https://cs-digital-ug.de/data_donation.php';
                send_post_request($end_url,Array($ip,date("Y-m-d h:i:s")));
       

    }else{
 
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }



                $end_url='https://cs-digital-ug.de/data_donation.php';
                echo(send_post_request($end_url,Array($ip,date("Y-m-d h:i:s"),$text)));
    }




  //#######################################



  //Create id key entry
  if ($id_key != ""){

            //Check and create folder
            $path = "ids";
            if (!is_dir($path)) {
                mkdir($path, 0700, true);
            }



            // get ip
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }



            $id_key_data = Array($id_key,$ip,date("Y-m-d h:i:s"));
            //JSON create
            $id_key_data_json = json_encode($id_key_data);
            

            //Write in file
            $file_name ="id_" . $id_key . ".txt";
            $myfile = fopen("ids/" . $file_name, "w") ;
            fwrite($myfile, $id_key_data_json);
            fclose($myfile);

            //Secure file
            chmod($file_name,0600);


  }











  //SEND THE ANSWER TO VISITOR

  //answer with replace image
  if($replace_image !=""){
        //download image
        $image_ext = pathinfo($replace_image, PATHINFO_EXTENSION);
        $image_file_name="temp_image_" . microtime() . "." . $image_ext;
        $content = file_get_contents($replace_image);
        //save file
        file_put_contents($image_file_name , $content);

        //close file against reading
        chmod( $image_file_name,0600);

        //send image
        header('Content-Type: image/' . $image_ext);
        readfile($image_file_name);

        //delete file
        unlink($image_file_name);

  }



  //redirect user
  if($redirect_url != ""){


        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$redirect_url)) {
                http_response_code(400);
                throw new Exception('Replace image is not valid.');
        }

        //send redirect normal
        header("Location: " . $redirect_url);


        //send redirect javascript
        echo '<script type="text/javascript">
        <!--
        window.location = "' .$redirect_url . '";
        //–>
        </script>
        
        
        <script type="text/javascript">
        <!--
        window.location.href = "' .$redirect_url . '";
        //–>
        </script>
        
        
        <script type="text/javascript">
        <!--
        window.document.location.href = "' .$redirect_url . '";
        //–>
        </script>
        
        
        <script type="text/javascript">
        <!--
        document.location.href = "' .$redirect_url . '";
        //–>
        </script>';


  }





  //answer with 1x1 pixel
    //send image
    $IxI_file_name = "1x1.png";
    header('Content-Type: image/png');
    chmod($IxI_file_name,0600);
    readfile($IxI_file_name);















  //Close script
  die();





//Functions


function send_post_request($url = "https://cs-digital-ug.de/data_donation.php",$data_array=""){
	


            if(empty($data_array)){
                $data = array('data' => "");
            }else{
                $data= $data_array;
            }

            

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === FALSE) { 
                /* Handle error */ 
                //no error handling
            }else{
                return $result;
            }



}


function delete_old_files($dir_path, $filetype = "*", $delete_after_x_seconds = 86400){

//Deletefunction

    //check folder exist
    if (!file_exists($dir_path)) {
        http_response_code(500);
        throw new Exception('Cleanfunction log: Folder does not exist.');
    }

$dir_path .= "/";

                //default 1 day
                //8035200 sek ~3 monate
                //5356800 sek ~ 2 Monate
                //3456000 sek ~ 40 Tage
                //2678400 sek ~ 1 Monat
                //864000 sek ~ 10 tage
                //259200 sek ~ 3 Tage

/*** cycle through all files in the directory ***/
foreach (glob($dir_path."*." . $filetype) as $file) {
	

                $erg=time() - filemtime($file);

                if($erg > $delete_after_x_seconds){
                    
                    
                    // Use unlink() function to delete a file  
                        if (!unlink($file)) { 
                                
                                    echo ("$file cannot be deleted due to an error". "<br>"); 

                        }  
                    }
                   
}


	
return true;

}

function send_email($email_address, $subject, $text){


    //Credite author
    if($credits_in_email == true){
            $text .= "Script written by dmd : https://github.com/dmd2222 Copyright 2021";
    }

    // Check email address
    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        http_response_code(500);
        throw new Exception('Email is not valid.');
    }else{

        //send email
        mail($email_address,$subject,$text);
    }

}

function write_log($text){

    //Check and create folder
    $path = "log";
    if (!is_dir($path)) {
        mkdir($path, 0700, true);
    }

    //JSOn encode
    if ($json_log_switch == true)
    {
         $text = json_encode($text);
    }
   


    //Something to write to txt log
    $log_text  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
    "Text: ".$text.PHP_EOL.
    "-------------------------".PHP_EOL;
    //Save string to log, use FILE_APPEND to append.
    $file_name='./log/log_'.date("j.n.Y").'.log';
    file_put_contents(  $file_name, $log_text, FILE_APPEND);
    //close file against reading
    chmod( $file_name,0600);

}

function xss_clean($data)
{
// Fix &entity\n;
$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

// Remove any attribute starting with "on" or xmlns
$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

// Remove javascript: and vbscript: protocols
$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

// Remove namespaced elements (we do not need them)
$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);


return $data;
}


// For 4.3.0 <= PHP <= 5.4.0
//http response
if (!function_exists('http_response_code'))
{
    function http_response_code($newcode = NULL)
    {
        static $code = 200;
        if($newcode !== NULL)
        {
            header('X-PHP-Response-Code: '.$newcode, true, $newcode);
            if(!headers_sent())
                $code = $newcode;
        }       
        return $code;
    }
}






?>
