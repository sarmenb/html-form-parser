# html-form-parser
php class that parses an html form and displays an array of all the labels and form fields.

## Usage
```
include_once('class.parse.php');
$parse = new ParseHtml();
$parse->source = file_get_contents('source.html');
echo '<pre>';
$data = $parse->getfields();
print_r($data);
```
