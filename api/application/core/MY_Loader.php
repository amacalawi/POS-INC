<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Loader extends CI_Loader {

    public function __construct(){

    }

    /*public function model($model, $name = '', $db_conn = FALSE)
    {
        if (is_array($model))
        {
            foreach ($model as $file => $object_name)
            {
                // Linear array was passed, be backwards compatible.
                // CI already allows loading models as arrays, but does
                // not accept the model name param, just the file name
                if ( ! is_string($file)) 
                {
                    $file = $object_name;
                    $object_name = NULL;
                }
                parent::model($file, $object_name);
            }
            return;
        }

        // Call the default method otherwise
        parent::model($model, $name, $db_conn);
    }

    public function controller($file_paths){
        $CI = & get_instance();
        
        if (is_array($file_paths))
        {
            foreach ($file_paths as $file_path)
            {
                
                $file_path_arr = explode('/', $file_path);
                $file_name = end($file_path_arr);
                
                //if($filez == 'controllers')
                //{
                    $file_path = APPPATH.'controllers/'.$file_path.'.php';
                //} else {
                //    $file_path = APPPATH.'models/'.$file_path.'.php';
                //}
                $object_name = $file_name;
                $class_name = ucfirst($file_name);
             
                if(file_exists($file_path)){
                    require $file_path;
                 
                    $CI->$object_name = new $class_name();
                }
                else{
                    show_error("Unable to load the requested controller class: ".$class_name);
                }

            }
            return;
        }
    } */
}