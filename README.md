




------------------

# The repository \\rstoetter\\ctabledependencymanager-php

## description  

The class cDependencyManager is the main class of the repository \\rstoetter\\cdependencymanager-php.

The class cDependencyManager is a PHP class, which is able to resolve dependencies (ie classes, URLs, HTML scripts .. ). You feed the class with the known dependencies of the objects you are providing and it generates an array with the objects you provided now arranged in the correct order, which is defined by the dependencies of the specific objects


## How can I use it?

For example, if we want to create a HTML header, which loads the necessary modules in the correct order. One file after another and the dependant files should not be loaded until the files they are dependant from hav been loaded.

In order to get the correct order you must not know the correct order. It is enough to know, the implicit dependencies of each file. The class cDependencyManager will then do the rest for you.

## helper classes

There are no helper classes necessary to use the class cdependencymanager:

But you will need PHP 7 or later to use this repository

## Usage:  

```php

// 
// first we define a class cMyDependantClass, which contains the information we need and add two public methods, 
// cDependencyManager will use:
// The Method GetDependencyCompareValue( ) returns a (commonly unique) value, which is used by cDependencyManager to distinguish
// between the objects of cMyDependantClass
// The Method DependsOn( ) returns true, if the current object depends on the Object provided as argument
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
    // extra methods we need in order to get used by class cDependencyManager
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
        '#1 js file_without_dependencies 1'
);

$obj_module_01 = new cMyDependantClass(
        '#2 dependant from #1',
        array( '#1 js file_without_dependencies 1' )        
);

$obj_module_02 = new cMyDependantClass(
        '#3 dependant from #1',
        array( '#1 js file_without_dependencies 1' )        
);

$obj_module_03 = new cMyDependantClass(
        '#4 dependant from #3',
        array( '#3 dependant from #1' )        
);

$obj_module_04 = new cMyDependantClass(
        '#5 dependant from #3',
        array( '#3 dependant from #1' )        
);

$obj_module_05 = new cMyDependantClass(
        '#6 dependant from #2 and #3',
        array( '#3 dependant from #1', '#2 dependant from #1' )        
);

$obj_module_06 = new cMyDependantClass(
        '#7 dependant from #2 and #3',
        array( '#3 dependant from #1', '#2 dependant from #1' )        
);

$obj_module_07 = new cMyDependantClass(
        '#8 js file_without_dependencies 2',
        array(  )        
);

$obj_module_08 = new cMyDependantClass(
        '#9 dependant from #7',
        array( '#7 dependant from #2 and #3' )        
);

$obj_module_09 = new cMyDependantClass(
        '#10 dependant from #7',
        array( '#7 dependant from #2 and #3' )        
);

$obj_module_10 = new cMyDependantClass(
        '#11 dependant from #7',
        array( '#7 dependant from #2 and #3' )        
);

$obj_module_11 = new cMyDependantClass(
        '#12 dependant from #9',
        array( '#9 dependant from #7' )        
);

$obj_module_12 = new cMyDependantClass(
        '#13 dependant from #1',
        array( '#1 js file_without_dependencies 1' )        
);

$obj_module_13 = new cMyDependantClass(
        '#14 dependant from #1',
        array( '#1 js file_without_dependencies 1' )        
);

$obj_module_13 = new cMyDependantClass(
        '#15 js file_without_dependencies 1',
        array( )        
);

$obj_module_14 = new cMyDependantClass(
        '#15 js file_without_dependencies 1'
);


//
// after instantiating the module classes, we feed the class cDependencyManager with the modules
//

//
// create a new object of the class cDependencyManager
//
//
// For your convinience you could supply an optional array of objects as first parameter. The class cDependencyManager will then add 
// the objects to this array 
//

$a_optional = array(
    $obj_module_00,
    $obj_module_01
);

// feed the manager with optional data

$obj_dependency_manager = new cDependencyManager( $a_optional );

//
// feed the manager with the other data
// if the object added does not provide the expected methods or is not an object, the process will crash with an error message
// objects already added to the data base and null values will be ignored, in this case AddRule( ) will return false, otherwise true
//
// the second parameter is optional and defaults to true. If true, then the class cDependencyManager will abort, if the given object is 
// not valid ( is not an object or does not provide the additional methods, the class cDependencyManagerneeds to handle the rule base ).
// If the value is false, then a warning message will be printed, when an object is not alid. This object will be ignored by the class 
// cDependencyManager.

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

die( PHP_EOL . PHP_EOL . " progran finished in " . __FILE__ . ' on line ' . __LINE__  . PHP_EOL );





```


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
