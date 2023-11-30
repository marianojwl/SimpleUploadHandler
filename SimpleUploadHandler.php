<?php
namespace marianojwl\SimpleUploadHandler {
    class SimpleUploadHandler {
        protected $multiple;
        protected $allowed;
        protected $maxFileSize;

        public function __construct(bool $multiple = false, $allowed = ["png"],int $maxFileSize = 5000000) {
            $this->multiple = $multiple;
            $this->allowed = $allowed;
            $this->maxFileSize = $maxFileSize;
        }
        
        public function getAcceptAttributeValue() {
          return "." . implode(", .",$this->allowed);
        }
        public function getForm(string $action, string $fileInputName = "myFile", string $fileInputId = "myFile", array $otherFields = []) {
            $html = '<form action="'.$action.'" method="post" enctype="multipart/form-data">' . PHP_EOL;
            foreach($otherFields as $of)
              $html .= $of.PHP_EOL;
            $html .= '<input accept="'.$this->getAcceptAttributeValue().'" type="file" name="'.$fileInputName.($this->multiple?'[]':'').'" id="'.$fileInputId.'"'.($this->multiple?' multiple':'').'>'. PHP_EOL;
            $html .= '<input type="submit" value="Upload" name="submit">'. PHP_EOL;
            $html .= '</form>' . PHP_EOL;
            return $html;
          }
          
          
          public function handleUpload(string $fileInputName = "myFile", string $target_dir = "./", string $newName = "") {
            $this->abortIfFileToBig();
            if (isset($_FILES[$fileInputName])) {
              if (is_string( $_FILES[$fileInputName]['name'] )) {
                  // Handle string case 
                  $_FILES[$fileInputName]['name'] = [ $_FILES[$fileInputName]['name'] ];
                  $_FILES[$fileInputName]["tmp_name"] = [ $_FILES[$fileInputName]["tmp_name"] ];
                  $_FILES[$fileInputName]["size"] = [ $_FILES[$fileInputName]["size"] ];
                  $_FILES[$fileInputName]["type"] = [ $_FILES[$fileInputName]["type"] ];
              }
            } else {
                // The 'myFile' key is not set in the POST data.
                return false;
            }
            $results_array = [];
            $numberOfFiles = count( $_FILES[$fileInputName]['name']  );
            for($i=0; $i<$numberOfFiles;$i++) {
              if( $this->isSizeOk($_FILES[$fileInputName]["size"][$i]) && $this->isTypeAllowed($_FILES[$fileInputName]["type"][$i]))  {
                if($newName == "")
                  $target_file = $target_dir . basename( $_FILES[$fileInputName]["name"][$i] );
                else
                  $target_file = $target_dir . $newName . "_" . $i . "." . explode("/",$_FILES[$fileInputName]["type"][$i])[1];
                $results_array[] = [
                  move_uploaded_file($_FILES[$fileInputName]["tmp_name"][$i], $target_file),
                  "path"=>$target_file,
                  "type"=>$_FILES[$fileInputName]["type"][$i],
                  "size"=>$_FILES[$fileInputName]["size"][$i]
                ];
              }
            }
            
            return $results_array;
          }
          public function isSizeOk($fileSize) {
            return $fileSize <= $this->maxFileSize;
          }
          public function isTypeAllowed($fileType) {
            return $this->isAllowedMime($fileType);
          }
          public function isAllowedMime($mime) {
            return in_array( explode("/",$mime)[1] , $this->allowed);
          }
          

        private function abortIfFileToBig() {
            // Get the maximum allowed POST size in bytes
            $maxPostSize = $this->parse_size(ini_get('post_max_size'));

            // Get the content length of the incoming POST request
            $contentLength = (int)$_SERVER['CONTENT_LENGTH'];

            // Compare content length to the maximum allowed size
            if ($contentLength > $maxPostSize) {
                // Display a custom error message
                echo "Error: The uploaded data exceeds the maximum allowed size of " . $this->format_size($maxPostSize);
                exit;
            }            
        }
        

        private function parse_size($size) {
            $unit = strtoupper(substr($size, -1));
            $number = (int)substr($size, 0, -1);
            switch ($unit) {
                case 'G':
                    $number *= 1024 * 1024 * 1024;
                    break;
                case 'M':
                    $number *= 1024 * 1024;
                    break;
                case 'K':
                    $number *= 1024;
                    break;
            }
            return $number;
        }

        // Helper function to format bytes into human-readable sizes
        private function format_size($bytes) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }
            return round($bytes, 2) . ' ' . $units[$i];
        }

    }
}