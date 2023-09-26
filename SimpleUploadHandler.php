<?php
namespace marianojwl\SimpleUploadHandler {
    class SimpleUploadHandler {
        protected $multiple;

        public function __construct(bool $multiple = false) {
            $this->multiple = $multiple;
        }
        public function getForm(string $action, string $fileInputName = "myFile", string $fileInputId = "myFile", array $otherFields = []) {
            $html = '<form action="'.$action.'" method="post" enctype="multipart/form-data">' . PHP_EOL;
            foreach($otherFields as $of)
              $html .= $of.PHP_EOL;
            $html .= '<input type="file" name="'.$fileInputName.($this->multiple?'[]':'').'" id="'.$fileInputId.'"'.($this->multiple?' multiple':'').'>'. PHP_EOL;
            $html .= '<input type="submit" value="Upload" name="submit">'. PHP_EOL;
            $html .= '</form>' . PHP_EOL;
            return $html;
          }
          
          public function handleUpload(string $inputFileName, array $permited = [], string $target_dir = "./") {
            $this->abortIfFileToBig();
            // Check if image file is a actual image or fake image
            $fileNamesResult = [];
            if(isset($_POST["submit"])) {
                if ( !is_array( $_FILES[$inputFileName]['name'] ) ) {
                  $_FILES[$inputFileName]['name'] = [ $_FILES[$inputFileName]['name'] ];
                  $_FILES[$inputFileName]["tmp_name"] = [ $_FILES[$inputFileName]["tmp_name"] ];
                  $_FILES[$inputFileName]["size"] = [ $_FILES[$inputFileName]["size"] ];
                }
                $countfiles = count($_FILES[$inputFileName]['name']);
                for($i=0;$i<$countfiles;$i++)
                {
                  $uploadOk = 1;
                  $target_file = $target_dir . basename($_FILES[$inputFileName]["name"][$i]);
                  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                  $check = getimagesize($_FILES[$inputFileName]["tmp_name"][$i]);
                  if($check !== false) {
                    echo "File ".$_FILES[$inputFileName]["name"][$i]." is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                  } else {
                    echo "File ".$_FILES[$inputFileName]["name"][$i]." is not an image.";
                    $uploadOk = 0;
                  }
                  // Check if file already exists
                  if (file_exists($target_file)) {
                    echo "Sorry, file already exists.";
                    $uploadOk = 0;
                  }
                  // Check file size
                  if ($_FILES[$inputFileName]["size"][$i] > 5000000) {
                    echo "Sorry, file ".$_FILES[$inputFileName]["name"][$i]." is too large.";
                    $uploadOk = 0;
                  }
                  // Allow certain file formats
                  if(!in_array($imageFileType, $permited, true)) {
                    echo "Sorry, only ".implode(", ",$permited)." files are allowed.";
                    $uploadOk = 0;
                  }
                  // Check if $uploadOk is set to 0 by an error
                  if ($uploadOk == 0) {
                    echo "Sorry, file ".$_FILES[$inputFileName]["name"][$i]." was not uploaded.";
                    $fileNamesResult[] = null;
                    // if everything is ok, try to upload file
                  } else {
                    if (move_uploaded_file($_FILES[$inputFileName]["tmp_name"][$i], $target_file)) {
                    echo "The file ". htmlspecialchars( basename( $_FILES[$inputFileName]["name"][$i])). " has been uploaded.";
                    $fileNamesResult[] = $target_file;
                    } else {
                    echo "Sorry, there was an error uploading ".$_FILES[$inputFileName]["name"][$i]." file.";
                    $fileNamesResult[] = null;
                    }
                  }
          
                  }
            }
            if(count($fileNamesResult)>1) 
              return $fileNamesResult;
            else
              return $fileNamesResult[0];
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
        

        // Continue processing the request if content length is within limits
        // Your normal request handling logic here

        // Helper function to parse size strings like "2M" or "512K" into bytes
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