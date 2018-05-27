# The repository \\rstoetter\\cdependencymanager-php

## description  

The class cDependencyManager is the main class of the repository \\rstoetter\\cdependencymanager-php.

The class cDependencyManager is a PHP class, which is able to resolve dependencies (ie between classes, URLs, HTML scripts .. ). 

You feed the class with the known dependencies of the objects you are providing and it generates an array with the objects you provided now arranged in the correct order, which is defined by the dependencies between the specific objects


## How can I use it?

For example, if we want to create a HTML header, which loads the necessary modules in the correct order. One file after another and the dependant files should not be loaded until the files they are dependant from have been loaded.

In order to get the correct order you must not know the correct order. It is enough to know the implicit dependencies of each file. Therefore you provide the basic rules for the file ( it's unique key and the files it depends directly on) and the class cDependencyManager will then calculate the correct order of the whole dependency structure for you.

## helper classes

There are no helper classes necessary to use the class cDependencyManager:

But you will need PHP 7 or later to use this repository

## Usage:  

```php

// 
// first we define a class cMyDependantClass, which contains the information we need in our own project 
// and add two public methods, the class cDependencyManager needs to manage the objects added:
// The method **GetDependencyCompareValue( )** returns a (commonly unique) value, which is used by 
// the class cDependencyManager to distinguish between the objects of cMyDependantClass
// The method **DependsOn( )** returns true, if the current object depends on the Object provided as argument
//

class cMyDependantClass {

    // data our class is using 
    
    protected $m_data_00 = null;
    protected $m_data_01 = null;

    //
    // extra data, which define the dependencies
    //
    
    // the url of the current module
    
    protected $m_url = '';          
    
    // an array with urls of modules the current module is depending on
    
    protected $m_a_dependencies = array( );              
    
    //
    //
    //
    
    function __construct(               // class cMyDependantClass
        string $url_module,             // the url of the current module
        array $a_urls = array( ),       // an array with urls of modules the current module is depending on
        $data_00 = null,
        $data_01 = null
    ) {
    
        $this->m_url = $url_module;
        $this->m_a_dependencies = $a_urls;
        $this->m_data_00 = $data_00;
        $this->m_data_01 = $data_01;    
    
    }   // function __construct( )
    
    function __destruct( ) {            // class cMyDependantClass
    
        $this->m_a_dependencies = null;
    
    }   // function __destruct( )   
    
    //
    // extra methods we need in order to enable the class cDependencyManagerto manage this object
    //    

    public function GetDependencyCompareValue( ) {
    
        return $this->m_url;
    
    }   // function GetDependencyCompareValue( )
    
    public function DependsOn( cMyDependantClass $obj ) : bool {
    
        return in_array( $obj->GetDependencyCompareValue( ), $this->m_a_dependencies ) ;
    
    }   // function DependsOn( )        
    

}   // class cMyDependantClass

//
// now let us create some objects representing modules 
// we are not supplying the normal data for $m_data_00 and $m_data_01, in order to leave the code readable
// instead of supplying HTML URLs we are supplying strings, which make the dependencies clear
// 

$obj_module_00 = new cMyDependantClass(
        '#01 file_without_dependencies'
);

$obj_module_01 = new cMyDependantClass(
        '#02 dependant from #1',
        array( '#01 file_without_dependencies' )        
);

$obj_module_02 = new cMyDependantClass(
        '#03 dependant from #1',
        array( '#01 file_without_dependencies' )        
);

$obj_module_03 = new cMyDependantClass(
        '#04 dependant from #3',
        array( '#03 dependant from #1' )        
);

$obj_module_04 = new cMyDependantClass(
        '#05 dependant from #3',
        array( '#03 dependant from #1' )        
);

$obj_module_05 = new cMyDependantClass(
        '#06 dependant from #2 and #3',
        array( 
            '#03 dependant from #1', 
            '#02 dependant from #1' 
        )        
);

$obj_module_06 = new cMyDependantClass(
        '#07 dependant from #2 and #3',
        array( 
            '#03 dependant from #1', 
            '#02 dependant from #1' 
        )        
);

$obj_module_07 = new cMyDependantClass(
        '#08 file_without_dependencies',
        array(  )        
);

$obj_module_08 = new cMyDependantClass(
        '#09 dependant from #7',
        array( '#07 dependant from #2 and #3' )        
);

$obj_module_09 = new cMyDependantClass(
        '#10 dependant from #7',
        array( '#07 dependant from #2 and #3' )        
);

$obj_module_10 = new cMyDependantClass(
        '#11 dependant from #7',
        array( '#07 dependant from #2 and #3' )        
);

$obj_module_11 = new cMyDependantClass(
        '#12 dependant from #9',
        array( '#09 dependant from #7' )        
);

$obj_module_12 = new cMyDependantClass(
        '#13 dependant from #1',
        array( '#01 file_without_dependencies' )        
);

$obj_module_13 = new cMyDependantClass(
        '#14 dependant from #1',
        array( '#01 file_without_dependencies' )        
);

$obj_module_14 = new cMyDependantClass(
        '#15 file_without_dependencies',
        array( )        
);

$obj_module_15 = new cMyDependantClass(
        '#16 file_without_dependencies'
);

$obj_module_16 = new cMyDependantClass(
        '#17 dependant from #7',
        array( '#07 dependant from #2 and #3' )        
);

$obj_module_17 = new cMyDependantClass(
        '#18 dependant from #5 and #2',
        array( 
            '#05 dependant from #3', 
            '#02 dependant from #1' 
        )        
);

//
// after instantiating the module classes, we feed the class cDependencyManager with the modules
//

//
// create a new object of the class cDependencyManager
//
//
// For your convinience you could supply an **optional** array of objects as first parameter. The class cDependencyManager will then add 
// the objects to this array 
//

$a_optional = array(
    $obj_module_00,
    $obj_module_01,
    new cMyDependantClass(
        '#19 file_without_dependencies'
    )
);

// create a new dependency manager and feed the manager with the optional data

$obj_dependency_manager = new new \rstoetter\cdependencymanager\cDependencyManager( $a_optional );

//
// feed the manager with the other data by calling it's method Add( )
// if the object added does not provide the expected methods or is not an object, the process will crash with an error message
// objects already added to the data base and null values will be ignored, in this case AddRule( ) will return false, otherwise true
//
// the second parameter is optional and defaults to true. If true, then the class cDependencyManager will abort, if the given object is 
// not valid ( is not an object or does not provide the additional methods, the class cDependencyManagerneeds to handle the rule base ).
// If the value is false, then a warning message will be printed, when an object is not valid. In this case the invalid  object will be
// ignored by the class cDependencyManager. If an object cannot be added, then Add( ) returns false.
//

$obj_dependency_manager->AddRule( $obj_module_00 ); // ignored as the module was added already
$obj_dependency_manager->AddRule( $obj_module_01 ); // ignored as the module was added already
$obj_dependency_manager->AddRule( $obj_module_02 );
$obj_dependency_manager->AddRule( $obj_module_03 );
$obj_dependency_manager->AddRule( $obj_module_04 );
$obj_dependency_manager->AddRule( $obj_module_05 );
$obj_dependency_manager->AddRule( $obj_module_06 );
$obj_dependency_manager->AddRule( $obj_module_07 );
$obj_dependency_manager->AddRule( $obj_module_07 );  // ignored as the module was added already
$obj_dependency_manager->AddRule( $obj_module_08 );
$obj_dependency_manager->AddRule( $obj_module_09 );
$obj_dependency_manager->AddRule( $obj_module_10 );
$obj_dependency_manager->AddRule( $obj_module_11 );
$obj_dependency_manager->AddRule( $obj_module_12 );
$obj_dependency_manager->AddRule( $obj_module_13 );
$obj_dependency_manager->AddRule( $obj_module_14 );

//
// retrieve an array with the objects in the correct order from class cDependencyManager
// if you set the second parameter of the method GetDependencies( ) to true, then cDependencyManager will not reset the provided
// array and add the dependencies to the current content of tthe array
//

$a_obj_in_order = array( );
$obj_dependency_manager-> GetDependencies( $a_obj_in_order, false );     

//
// print out the received list of objects
//

foreach( $a_obj_in_order as $obj_rule ) {
    echo PHP_EOL . $obj_rule->GetDependencyCompareValue( );
}

//
// end the program
//

die( PHP_EOL . PHP_EOL . " program finished in " . __FILE__ . ' on line ' . __LINE__  . PHP_EOL );


```

The code above will output the following result:

    #08 file_without_dependencies
    #19 file_without_dependencies
    #16 file_without_dependencies
    #15 file_without_dependencies
    #01 file_without_dependencies
    #13 dependant from #1
    #03 dependant from #1
    #02 dependant from #1
    #14 dependant from #1
    #04 dependant from #3
    #05 dependant from #3
    #06 dependant from #2 and #3
    #07 dependant from #2 and #3
    #18 dependant from #5 and #2
    #10 dependant from #7
    #11 dependant from #7
    #17 dependant from #7
    #09 dependant from #7
    #12 dependant from #9

## Installation

This project assumes you have composer installed. Simply add:

"require" : {

    "rstoetter/cdependencymanager-php" : ">=1.0.0"

}

to your composer.json, and then you can simply install with:

composer install


## Namespace

Use the namespace cDependencyManager in order to access the classes provided by the repository cdependencymanager-php

## More information

See the [project wiki of cdependencymanager-php](https://github.com/rstoetter/cdependencymanager-php/wiki) for more technical informations.
