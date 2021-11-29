<?php

/*
Copyright (c) CS-Digital UG (hatungsbeschränkt) https://cs-digital-ug.de/ 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/*Version: 1.0.2.0 First Edition */


// Get values
$get_key="em";
if(isset($_GET[$git_key]) && !empty($_GET[$git_key]))
{
    $email_base64 = xss_clean($_GET[$git_key]) ;
}else{
    $email_base64 = "";
}


$get_key="ri";
if(isset($_GET[$git_key]) && !empty($_GET[$git_key]))
{
    $replace_image = xss_clean($_GET[$git_key]) ;
}else{
    $replace_image = "";
}

$get_key="re";
if(isset($_GET[$git_key]) && !empty($_GET[$git_key]))
{
    $redirect_url = xss_clean($_GET[$git_key]) ;
}else{
    $redirect_url = "";
}



$get_key="ai";
if(isset($_GET[$git_key]) && !empty($_GET[$git_key]))
{
    $additional_info = xss_clean($_GET[$git_key]) ;
}else{
    $additional_info = "";
}





//Convert values
if($email_base64 != ""){
    $email_clear = xss_clean(base64_decode($email_base64)) ;
}



// Check values
// Check email address
if (!filter_var($email_clear, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    throw new Exception('Email is not valid.');
  }

//Check replace image
if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$replace_image)) {
    http_response_code(400);
    throw new Exception('Replace image is not valid.');
  }

  //Check redirect url
  if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$redirect_url)) {
    http_response_code(400);
    throw new Exception('Replace image is not valid.');
  }

  //Check additional info
  if(preg_match("/[^a-zA-Z0-9]+/",$additional_info)==False){
    $additional_info = preg_replace("/[^a-zA-Z0-9]+/", "", $additional_info);
  }


  //main program
//##########################################################
  //send email and/or logfile

    //generate all server vars
    $all_server_vars ="";
    foreach($_REQUEST AS $key => $value)
      {
          $all_server_vars .= $key.": ".$value.PHP_EOL;
      }

  $subject="";

  $text="";

  //add all server vars
  $text .= "all_server_vars:". $all_server_vars;
  //add all server vars
  $text .= "additional_info:". $additional_info;




  if($email_clear != ""){
        // send email
        send_email($email_clear, $subject, $text);

  }elseif ($email_clear == "both@") {
      //Write in logfile and send email
    
      //Write in logfile
      write_log($subject . ":" . $text);
    
        // send email
        send_email($email_clear, $subject, $text);

  }else{
        //Write in logfile
        write_log($subject . ":" . $text);
  }




  //answer with replace image
  if($replace_image !=""){
        //download image
        $image_ext = pathinfo($replace_image, PATHINFO_EXTENSION);
        $image_file_name="image_" . microtime() . "." . $image_ext;
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






  //Close script
  die();





//Functions


function send_email($email_address, $subject, $text){


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

    //Something to write to txt log
    $log_text  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
    "Text: ".$text.PHP_EOL.
    "-------------------------".PHP_EOL;
    //Save string to log, use FILE_APPEND to append.
    $file_name='./log_'.date("j.n.Y").'.log';
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