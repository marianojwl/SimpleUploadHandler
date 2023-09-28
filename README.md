# SimpleUploadHandler Class

The SimpleUploadHandler class is a PHP utility for handling file uploads with ease. It provides a straightforward way to create HTML forms for file uploads, validate file types and sizes, and process uploaded files to move them to a specified directory.

## Features

- Create HTML forms for file uploads.
- Control whether multiple files can be uploaded in a single request.
- Specify the allowed file types.
- Set a maximum file size limit.
- Handle uploaded files, moving them to a target directory.
- Check file size and type before processing.

## Installation

You can use the SimpleUploadHandler class by including it in your PHP project. Download the [`SimpleUploadHandler.php`](https://github.com/marianojwl/SimpleUploadHandler/blob/main/SimpleUploadHandler.php) file from the GitHub repository and include it in your project.

```php
require_once 'SimpleUploadHandler.php';
```

## Usage

Here's how to use the SimpleUploadHandler class in your PHP project:

### 1. Include the class in your PHP file:

```php
use marianojwl\SimpleUploadHandler\SimpleUploadHandler;
```

### 2. Create an instance of the SimpleUploadHandler class:

```php
// Instantiate the class with optional parameters
$uploadHandler = new SimpleUploadHandler(
    $multiple = false,      // Set to true if you want to allow multiple file uploads
    $allowed = ["png"],    // Specify allowed file types (e.g., ["png", "jpg"])
    $maxFileSize = 5000000  // Set the maximum file size (in bytes)
);
```

### 3. Generate an HTML form for file uploads:

```php
// Create an HTML form for file uploads
$action = "upload.php"; // Set the form's action attribute to the target upload script
$fileInputName = "myFile"; // Name of the file input element
$fileInputId = "myFile"; // Id of the file input element
$otherFields = [
    '<input type="hidden" name="key" value="value">', // Add hidden input tags or other form fields
];

$formHtml = $uploadHandler->getForm($action, $fileInputName, $fileInputId, $otherFields);
echo $formHtml;
```

### 4. Handle file uploads:

```php
// Handle file uploads when the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $targetDirectory = "./uploads/"; // Specify the target directory for uploaded files
    $results = $uploadHandler->handleUpload($fileInputName, $targetDirectory);

    // Process the results
    foreach ($results as $result) {
        if ($result[0]) {
            echo "File uploaded successfully. Path: " . $result["path"] . "<br>";
            echo "File type: " . $result["type"] . "<br>";
            echo "File size: " . $uploadHandler->format_size($result["size"]) . "<br><br>";
        } else {
            echo "Error uploading file.<br><br>";
        }
    }
}
```

## Validation

The SimpleUploadHandler class provides the following validation methods:

- `isSizeOk($fileSize)`: Check if a file's size is within the specified limit.
- `isTypeAllowed($fileType)`: Check if a file's MIME type is allowed.
- `isAllowedMime($mime)`: Check if a MIME type is in the list of allowed types.

## Note

This class also includes functions to handle cases where uploaded files exceed PHP's maximum allowed POST size.

```php
// Private functions for handling maximum POST size exceeded cases
private function abortIfFileToBig()
private function parse_size($size)
private function format_size($bytes)
```

## Example

For a complete example of how to use the SimpleUploadHandler class, please refer to the [`example.php`](https://github.com/marianojwl/SimpleUploadHandler/blob/main/example.php) file in this repository.

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/marianojwl/SimpleUploadHandler/blob/main/LICENSE) file for details.