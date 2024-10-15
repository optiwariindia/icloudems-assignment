# website
## The concept
It's a composer based basic boilerplate code for PHP 7/8 development.
## Installation

To install using composer use following command

``` composer require optiwariindia/website ```

## How to use

### Setup
Configure setup page for your project. The predefined strucutre checks the required php extensions, apache modules, cofiguration files and email/database connectivity parameters.
You can extend setup class as below:

``` class setup extends \optiwariindia\website\setup { ... } ```


### Config
The config class is designed to store basic configurations of the application. It stores database configuration, email configuration, API Keys, Encryption keys, Integration parameters and other static resources that are not frequently changed and hence cannot be saved into database. 
You can extend config class as below:

``` class config extends \optiwariindia\website\config { ... } ```
### Request
This class is designed to handle request parameters like inputs, method, timestamp, url, useragent and action parameters.
The class can be extended to use in your project as below:

``` class request extends \optiwariindia\website\request {...} ```
### Session
This class is designed to manage user's session using PHP Session variables. This class takes care of initialization of session only once in the page, store parameters into your session variable and fetch them whenever required.
The class can be extended to use in your project as below:

``` class session extends \optiwariindia\website\session {...} ``` 
### View
This class is designed to manage views using twig template engine. Besides twig template engine, it also takes care of JSON outputs and HTML Output as variable. You can extend this class as below:

``` class view extends \optiwariindia\website\view {...}```

#### Setup view directory

``` view::dir(_path_to_view_directory_); ```

#### Render a page

``` view::render(_filename_in_view_directory,Array_of_variables_to_be_parsed)```

#### Read HTML Page into a variable

``` view::get(_filename_in_view_directory,Array_of_variables_to_be_parsed)```

#### Generate JSON Output for API

``` view::api(Array_of_variables_to_be_parsed)```

### Module
This class is created to create plugable modules in your project. This class provides support of basic functions related to database and debugging. This class is dependent on optiwariindia\database and mysqli extension. You can create plugable modules by extending this class to your module as follows:

``` class module1 extends optiwariindia\website\module {...} ```

#### Debug option
You can use trace method to dump a variable in json format and stop execution of the code at the point as shown below: 

<code> class module1 extends optiwariindia\website\module { 
<br>&nbsp;    public function someFunctionName(someParameter){
<br>&nbsp;&nbsp;        //Some operation here
<br>&nbsp;&nbsp;        self::trace(VariableToBeTraced)
<br>&nbsp;    }
<br>}</code>

#### Connecting Database
You can use init to initalize database within any module and get access to methods select,insert,update,delete and query as shown in example below:

<code>
class module1 extends optiwariindia\website\module {
    <br>&nbsp;public function FunctionName($requiredParameters){
        <br>&nbsp;&nbsp; $db=self::init();
        <br>&nbsp;&nbsp; $info=$db->select(__table__,__fields__,__clauses__);
    <br>&nbsp;}
<br>}</code>

We welcome suggestions and enhancements. Thanks for you cooperation