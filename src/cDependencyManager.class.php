<?php

namespace rstoetter\cdependencymanager;

    class cDependencyManagerElement {
    
        protected $m_data = null;              // the consumer's class object
        
        private $m_rearranged = false;          // true, if the settings are prepared for sorting after the scann for relations
        
        public $m_a_obj_before = array( );     // objects of same type as $m_data, the m_data-object is depending on
        public $m_a_obj_after = array( );     // objects of same type as $m_data, referring to the m_data-object
        
        protected $m_index = -1;
        
        // 
        //
        //
        
        function __construct( int $index, $obj ) {           // cDependencyManagerElement
        
            $this->m_data = $obj;
            $this->m_index = $index;
        
        }   // function __construct( )
        
        
        function __destruct( ) {           // cDependencyManagerElement
        
        }   // function __destruct( )
    
        public function GetObject( ) {           
        
            return $this->m_data;
        
        }   // function GetObject( )
        
        public function AddDependency( cDependencyManagerElement $obj ) {           
        
            $this->m_a_obj_dependencies[] = $obj;
        
        }   // function AddDependency( )            
        
        
        public function AddBefore( cDependencyManagerElement $obj ) {           
        
            $this->m_a_obj_before[] = $obj;
        
        }   // function AddBefore( )            
        
        public function AddAfter( cDependencyManagerElement $obj ) {           
        
            if ( ! in_array( $obj, $this->m_a_obj_after ) ) $this->m_a_obj_after[] = $obj;
        
        }   // function AddAfter( )          
        
        public function ComesBefore( cDependencyManagerElement $obj ) : bool {
        
            return in_array( $this, $obj->m_a_obj_before );
        
        }   // function ComesBefore( )
        
        public function CheckRelations( cDependencyManagerElement $obj ) : bool {
        
            return $this->_FoundRelation( $this, $obj );
        
        }   // function CheckRelations( )
        
        protected function ComesAfter( cDependencyManagerElement $obj ) : bool {
        
            return in_array( $obj, $this->m_a_obj_before );
        
        }   // function ComesAfter( )
        
        protected function IsBefore( cDependencyManagerElement $obj ) : bool {
        
            return in_array( $this, $obj->m_a_obj_before );
        
        }   // function IsBefore( )
        
        
        public function HasBefore( ) : bool {
        
            return count( $this->m_a_obj_before );
        
        }   // function HasBefore( )
        
        public function SwapBefore( int $level ) {
        
            if ( count( $this->m_a_obj_before > 1 ) ) {
            
                $org = count( $this->m_a_obj_before ) - 1 -1;
                $replace = count( $this->m_a_obj_before ) - 1 + $level + 1 -1;
                
                $tmp = $this->m_a_obj_before[ $org ] ;
                        
                $this->m_a_obj_before[ $org ]  = $this->m_a_obj_before[ $replace ] ;
                $this->m_a_obj_before[ $replace ]  = $tmp;
            }
                    
        }   // function SwapBefore( )        
        
        protected function DistanceTo( 
            cDependencyManagerElement $obj
        ) : int {
        
            $ret = -1;
            
            for( $i = 0; $i < count( $this->m_a_obj_before ); $i++  ) {
                    if ( $this->m_a_obj_before[ $i ] == $obj ) {
                        $ret = $i;
                        break;
                    }
            }
            
            return $i;
            
        }   // function DistanceTo( )      
        
        public function Rearrange( ) {
        
            //
            // the data supplied at start are not in order, so we have to change this
            //
            
            if ( ! $this->m_rearranged ) {            
            
                usort( 
                    $this->m_a_obj_before, 
                    function( 
                        cDependencyManagerElement $a, 
                        cDependencyManagerElement $b 
                    ) {
                    
                        $ret = 0;
            
                        if ( $a->ComesBefore( $b ) ) {
                            $ret =  -1;
                        } elseif ( $b->ComesBefore( $a ) ) {
                            $ret = 1;
                        }
                        
                        return $ret;
                        
                    }
                
                );
                
                usort( 
                    $this->m_a_obj_after, 
                    function( 
                        cDependencyManagerElement $a, 
                        cDependencyManagerElement $b 
                    ) {
                    
                        $ret = 0;
            
                        if ( $a->ComesBefore( $b ) ) {
                            $ret =  -1;
                        } elseif ( $b->ComesBefore( $a ) ) {
                            $ret = 1;
                        }
                        
                        return $ret;
                        
                    }
                
                );
                
        
                $this->m_rearranged = true;
            
            }
            
        
        }   // function Rearrange( )
        
        public function CompareWith( 
            cDependencyManagerElement $obj
        ) : int {
        
            //
            // if the value of $obj is greater than this object's value then the value will be < 0 
            // if the value of this object's value is greater than the value of obj then the value will be < 0
            // if thae value of his object's value equals the value of objthe we return 0
            //
            
/*            echo 
                "\n ======================================" .
                "\n CompareWith with " . 
                $this->m_data->GetDependencyCompareValue( ) .
                " and " . 
                $obj->m_data->GetDependencyCompareValue( )                                
            ;*/            

            $ret = 0;
            
            if ( ! count( $this->m_a_obj_before ) && ( ! count( $this->m_a_obj_after ) ) ) {
                $ret = -1;
                // echo "\n -> adjusted ret because the this obj has no count( before ) to $ret";
            } elseif ( ! count( $obj->m_a_obj_before ) && ( ! count( $obj->m_a_obj_after ) ) ) {
                $ret = 1;
                // echo "\n -> adjusted ret because the this obj has no count( before ) to $ret";
            } elseif ( ! count( $this->m_a_obj_before ) ) {
                $ret = -1;
                // echo "\n -> adjusted ret because the this obj has no count( before ) to $ret";
            } elseif ( ! count( $obj->m_a_obj_before ) ) {
                $ret = 1;
                // echo "\n -> adjusted ret because the other obj has no count( before ) to $ret";
            } elseif ( ( ! $this->HasBefore( ) ) &&  ( ! $obj->HasBefore( ) ) ) {
            
                $ret = ( count( $this->m_a_obj_after ) - count( $obj->m_a_obj_after ) );
                
                // echo "\n-->No predecessors: " . $this->m_data->GetDependencyCompareValue( ) . ' and '. $obj->GetObject( )->GetDependencyCompareValue( ) . ' result in ' . $ret;
                
            } elseif ( ! $this->HasBefore( ) ) {
            
                // echo "\n-->" . $this->m_data->GetDependencyCompareValue( ) . ' comes before '. $obj->GetObject( )->GetDependencyCompareValue( ) . ' because the first latter has no before';
                
                $ret =  -1;
                
            } elseif ( ! $obj->HasBefore( ) ) {
            
                // echo "\n-->" . $this->m_data->GetDependencyCompareValue( ) . ' comes after '. $obj->GetObject( )->GetDependencyCompareValue( ) . ' because the latter has no before';
                $ret =  1;
                
            }  elseif ( $this->ComesBefore( $obj ) ) {
                $ret = -1;
                // echo "\n-->" . $this->m_data->GetDependencyCompareValue( ) . ' comes before '. $obj->GetObject( )->GetDependencyCompareValue( ) . " with distance = {$ret}";
                
            } elseif ( $obj->ComesBefore( $this ) ) {
                $ret = 1;
                // echo "\n-->" . $this->m_data->GetDependencyCompareValue( ) . ' comes after '. $obj->GetObject( )->GetDependencyCompareValue( ) . " with distance = {$ret}";
                
            } else {
            
                if ( $this->m_a_obj_before[ 0 ] ==  $obj->m_a_obj_before[ 0 ] ) {
                    if ( ( $this->m_a_obj_before[ 0 ] ) != ( $obj->m_a_obj_before[ 0 ] ) ) {
                        $ret = ( $this->m_a_obj_before[ 0 ]->ComesBefore( $obj->m_a_obj_before[ 0 ]) ? -1 : 1 );
//                         echo 
//                             "\n adjusted before - " . 
//                             $this->m_data->GetDependencyCompareValue( ) . 
//                             "differing start with ret = $ret"
//                         ;
                    } else {
                        if ( in_array( $this->m_a_obj_before[ count( $this->m_a_obj_before ) -1 ], $obj->m_a_obj_before ) ) {
                            $ret = -1;
                        } elseif ( in_array( $obj->m_a_obj_before[ count( $obj->m_a_obj_before ) -1 ], $this->m_a_obj_before ) ) {
                            $ret = 1;
                        } else {
                            // $ret = count( $this->m_a_obj_before ) - count( $obj->m_a_obj_before );                                
                            
                            $ret = $obj->m_index - $this->m_index;
                        }
                        
//                         echo 
//                             "\n adjusted before " .
//                             $this->m_data->GetDependencyCompareValue( ) . 
//                             " compare last values returns $ret"
//                         ;                        
                        
                    
                    }
                
                } 
            
                if ( $this->m_a_obj_before[ 0 ] !=  $obj->m_a_obj_before[ 0 ] ) {
                
                    echo "\n unsupported combination";
                
                    /*
                
                    $found = false;
            
                    for ( $i = 0; $i < count( $this->m_a_obj_before ); $i++ ) {
                    
                        if ( $this->m_a_obj_before[ $i ] == $obj->m_a_obj_before[ 0 ] ) {
                        
                            $j = $i + 1 ;
                            $k = 1;
                            
                            while ( true ) {
                            
                                if ( $j == count( $this->m_a_obj_before ) ) {
                                    // shorter distance
                                    $ret = -1;
                                    break;
                                    
                                } elseif ( $k == count( $obj->m_a_obj_before ) ) {
                                    // longer distance
                                    $ret = 1;
                                    break;
                                    
                                } else if ( $this->m_a_obj_before[ $j ] != $obj->m_a_obj_before[ $k ] ) {
                                
                                    $ret = -1;
                                    break;
                                
                                } else {
                                    $j++;
                                    $k++;                                
                                }
                            
                            }
                            
                            if ( $ret ) {
                                break;
                            }                            

                            $found = ( $ret != 0 );
                            echo "\n-->" . $this->m_data->GetDependencyCompareValue( ) . ' compare distances '. $obj->GetObject( )->GetDependencyCompareValue( ) . " with ret = {$ret}";                            
                        
                        }
                    }
                    
                    
                    
                    if ( ! $found ) {
                    
                        for ( $i = 0; $i < count( $obj->m_a_obj_before ); $i++ ) {
                        
                            if ( $obj->m_a_obj_before[ $i ] == $this->m_a_obj_before[ 0 ] ) {

                                $ret = count( $obj->m_a_obj_before ) - $i - count( $this->m_a_obj_before );
                                $found = true;
                                echo "\n-->" . $this->m_data->GetDependencyCompareValue( ) . ' compare paths '. $obj->GetObject( )->GetDependencyCompareValue( ) . " with ret = {$ret}";                            
                                break;
                            
                            }
                        }                                        
                    }
                    
                    if ( ! $found ) {
                        echo "\n-->" . $this->m_data->GetDependencyCompareValue( ) . ' compare paths '. $obj->GetObject( )->GetDependencyCompareValue( ) . " no result with ret = {$ret}";                                                
                    }
                    
                    
                    if ( ! $ret ) {
                        if ( ! count( $this->m_a_obj_after ) ) {
                            $ret = -1;
                            echo "\n -> adjusted ret because this object has no m_count_after to $ret";
                        } elseif ( ! count( $obj->m_a_obj_after ) ) {
                            $ret = 1;
                            echo "\n -> adjusted ret because the other obj has no m_count_after to $ret";
                        } else {
                            $ret = count( $this->m_a_obj_before ) - count( $obj->m_a_obj_before );                                
                            echo "\n -> adjusted ret by count( before ) to $ret";
                        }                    
                    }
                    
                    */
                
                }
            
            
            }
            
            
/*            echo "\n dist between " . 
                $this->m_data->GetDependencyCompareValue( ) .
                " and " . 
                $obj->m_data->GetDependencyCompareValue( ) .
                " results in ".
                $ret .
                "\n ======================================"
            ; */           
    
            return $ret;
            
        
        }   // function CompareWith( )
        
        protected function _FoundRelation( 
                cDependencyManagerElement $obj_depends,
                cDependencyManagerElement $obj_refers,
                int $level = 0
        ) {
        
            $ret = false;
            
            $space = '|' . str_pad( '', $level * 4  );
            
//             if ( ! $level ) {
//                 echo "\n--------------------------------------";
//             }
//             
//             echo "\n " . $space . "relation level {$level}  between '" . $obj_depends->m_data->GetDependencyCompareValue( ) . "' and '" . $obj_refers->m_data->GetDependencyCompareValue( ) . "' ?";
            
//             $obj_depends->Dump( $level + 2 );
//             $obj_refers->Dump( $level + 2 );
        
            if ( $obj_depends == $obj_refers ) {
            
                // echo "\n" . $space . " -> NO, same objects " ;
                
                $ret = false;
                
            }  elseif ( in_array( $obj_refers, $obj_depends->m_a_obj_before ) ) {
            
                $obj_refers->AddAfter( $obj_depends );
            
                // echo "\n" . $space . " -> NO ($level), - hint already listed  in depending object " . $obj_depends->m_data->GetDependencyCompareValue( );
                
                $ret = false;
                
            } elseif ( in_array( $obj_depends, $obj_refers->m_a_obj_before ) ) {
            
                // echo "\n" . $space . " -> TRUE ($level), - vice versa - hint already listed  in referring object " . $obj_refers->m_data->GetDependencyCompareValue( );
                
                $ret = true;
                
            } elseif ( ! count( $obj_refers->m_a_obj_before ) ) {
            
                // echo "\n" . $space . " -> NO, no before objects in " . substr( $obj_refers->m_data->GetDependencyCompareValue( ), 0, 5 );
                
                $ret = false;
                
            } else {            
                foreach( $obj_refers->m_a_obj_before as $obj_before ) {
                    if ( $this->_FoundRelation( $obj_depends, $obj_before, $level + 1 ) ) {
                        $ret = true;
                        if ( ! $obj_depends->IsBefore( $obj_refers ) ) {
                            $obj_refers->AddBefore( $obj_depends );                                                    
                            // echo "\n" . $space . "adding " . $obj_depends->m_data->GetDependencyCompareValue( ) . " to " . $obj_refers->m_data->GetDependencyCompareValue( );
                        }
                        
                    } else {
                    
                    }
                }
                
            }
            
            return $ret;
        
        }   // function _FoundRelation( )
        
        
        public function Dump( $level = 0 ) {
        
            $space = str_pad( '', $level * 4  );
        
            echo "\n" . $space . "Dump of cDependencyManagerElement:";
            if ( $level ) {
                echo " level = {$level}";
            }
            
            echo "\n" . $space . ": " . $this->m_data->GetDependencyCompareValue( );
            
            if ( count( $this->m_a_obj_before ) ) {
                echo "\n" . $space .  "  dependant from ";
                foreach( $this->m_a_obj_before as $obj ) {
                    echo "\n" . $space . "   " . $obj->GetObject( )->GetDependencyCompareValue( );
                }
            } else {
                echo "\n has no predecessors  ";
            }
            
            if ( count( $this->m_a_obj_after ) ) {
                echo "\n" . $space .  "  referers: ";
                foreach( $this->m_a_obj_after as $obj ) {
                    echo "\n" . $space . "   " . $obj->GetObject( )->GetDependencyCompareValue( );
                }
            } else {
                echo "\n has no referrals  ";
            }
            
            
            echo PHP_EOL;
        
        }   // function Dump( )
        
    
    }   // class cDependencyManagerElement
    
    class cDependencyManager {
    
        //
        // the dependeny manager class
        // TODO: use a specialized binary to do this job
        //
        
        static public $m_message_on_abort = 'Aborting..';
        static public $m_message_no_object = 'error: expected an object and got type ';
        static public $m_message_no_method_add = 'error: the received object does not provide a method \'GetDependencyCompareValue( )\'';
        static public $m_message_no_method_dependson = 'error: the received object does not provide a method \'DependsOn( )\'';
        
        //
    
        protected $m_a_obj_entries = array( );        // array consisting of type cDependencyManagerElement
        
        //
        
        function __construct( array $a_obj_rules = array( ) ) {               // cDependencyManager                        
        
            foreach( $a_obj_rules as $obj_rule ) {
                $this->AddRule( $obj_rule );
            }
        
        }   // function __construct( )
        
        function __destruct( ) {                // cDependencyManager
        }   // function __destruct( )         
        
        public function AddRule( 
                    $obj,
                    bool $abort_on_error = true
        ) : bool {        
        
            $ret = false;
            
            if ( ! is_null( $obj ) ) {
        
                $msg = '';
                if ( ! is_object( $obj ) ) {        
                    $msg = PHP_EOL . self::$m_message_no_object . gettype( $obj );
                } elseif ( ! method_exists( $obj, 'GetDependencyCompareValue' ) ) {        
                    $msg = PHP_EOL . self::$m_message_no_method_add;
                } elseif ( ! method_exists( $obj, 'DependsOn' ) ) {        
                    $msg = PHP_EOL . self::$m_message_no_method_dependson;
                }
                
                if ( strlen( $msg ) ) {
                
                    $msg_die = PHP_EOL . self::$m_message_on_abort . PHP_EOL;
                
                    if ( PHP_SAPI != 'cli' ) {
                        echo "<pre>";
                        $msg = nl2br( $msg );
                        $msg_die = nl2br( $msg_die );
                    }
                    
                    debug_print_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
                    
                    echo $msg;
                    
                    if ( $abort_on_error ) die( $msg_die );            
                    
                    if ( PHP_SAPI != 'cli' ) {
                        echo "</pre>";
                    }
                    
                } else {    
                    if ( ! $this->IsAlreadyKnown( $obj ) ) {                        
                        $ret = true;
                        $this->m_a_obj_entries[] = $obj;
                    }
                }
                
            }
            
            return $ret;
        
        }   // function AddRule( )        
        
        public function IsAlreadyKnown( $obj ) : bool {
        
            foreach( $this->m_a_obj_entries as $obj_known ) {
                if ( $obj_known == $obj ) {
                    return true;
                }
            }
        
            return false;
        
        }   // function IsAlreadyKnown( )
        
        protected function ResolveDependencies( array & $ary ) {
        
            //
            // resolve the dependencies
            //
            
            $a_obj_chained = array( );
            
            // collect the objects of the and the objects which are depending on the specific object
            
            $count = 0;
            foreach( $this->m_a_obj_entries as $obj ) {
            
                $a_obj_chained[] = new cDependencyManagerElement( $count++, $obj );
                
            }
            
            
            foreach( $a_obj_chained as $obj_chained ) {
            
                foreach( $a_obj_chained as $obj_tst ) {
                    if ( $obj_chained->GetObject( )->DependsOn( $obj_tst->GetObject( )  ) ) {
                        $obj_chained->AddBefore( $obj_tst );
                    }
                }
            
            }
            
            
            // resolve the dependencies
            
            foreach( $a_obj_chained as $obj1 ) {
            
                foreach( $a_obj_chained as $obj2 ) {
                
                    if ( $obj1 != $obj2 ) {
                    
                        $obj1->CheckRelations(  $obj2 ) ;;
                    }
                
                }
            
            }
            
            // resort the dependeny lists of all objects after resolving the dependencies
            
            foreach( $a_obj_chained as & $obj_chained ) {
            
                $obj_chained->Rearrange( );
            
            }  
            
            // resort by dependencies
            
            usort( 
                    $a_obj_chained,
                    function (
                        cDependencyManagerElement $a, 
                        cDependencyManagerElement $b
                    ) {                     

                        return $a->CompareWith( $b );                        
                        
                    } 
            );                        
            
            //
            
            $this->m_a_obj_entries = array( );
            
            foreach( $a_obj_chained as $obj ) {
                $this->m_a_obj_entries[] = $obj->GetObject( );
            
            }
            
            
        }   // function ResolveDependencies( )

        public function GetDependencies( array & $ary, bool $do_add = false ) {
        
            if ( ! $do_add ) {
                $ary = array( );
            }
            
            $this->ResolveDependencies( $ary );
            
            foreach( $this->m_a_obj_entries as $obj ) {
                $ary[] = $obj;
            }
        
        }   // function GetDependencies( )
    
    
    }   // class cDependencyManager
    
/*
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

    
    function Test_cDependencyManager( ) {
    
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
                    '#02 dependant from #1',
                    '#05 dependant from #3',        // ignored, because the dependency is already known
                    '#02 dependant from #1'         // ignored, because the dependency is already known
                    
                )        
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
            $obj_module_01,
            new cMyDependantClass(
                '#19 file_without_dependencies'
            )
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
        $obj_dependency_manager->AddRule( $obj_module_15 );
        $obj_dependency_manager->AddRule( $obj_module_16 );
        $obj_dependency_manager->AddRule( $obj_module_17 );

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
    
    }   // function Test_cDependencyManager( )
    
    Test_cDependencyManager( );

*/

?>