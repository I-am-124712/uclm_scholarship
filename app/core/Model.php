<?php


class Model {

    protected $server = 'RAVINSLENOVO\\RAVIN_LOUIS_0X45';
    protected $connectionInfo = array(
        "Database" => "DTRDB",
    );
    protected $connectionResource;
    protected $fields = [];
    protected $params = array();

    protected $query_string = '';
    protected $prepared_statement;
    protected $query;


    /** Creates a Model object with the given fields. These fields
    * will be the corresponding fields in this model's table in the database.
    * @param $fields an associative array having the column name as key.
    */
    public function create($fields = []){
        $this->fields = $fields;
        return $this;
    }


    // debugging purposes only
    public function get_fields($key = ''){ 
        if($key == null || $key === '')
            return $this->fields; 
        else{
            if(isset($this->fields[$key]))
                return $this->fields[$key];
            else
                throw new Exception('Provided key is invalid.');
        }
    }
    /* This will be the final implementation of 
        retrieving the value of a Model object's field(s).
    */
    public function get($key = ''){ 
        if($key == null || $key === '')
            return $this->fields; 
        else{
            if(isset($this->fields[$key]))
                return $this->fields[$key];
            else
                throw new Exception('Provided key is invalid.');
        }
    }


    // Readies the connection to the SQLServer DB
    public function ready(){
        if($this->connectionResource === null)
        $this->connectionResource = sqlsrv_connect($this->server,$this->connectionInfo);
        return $this;
    }
    
    // Performs an INSERT operation into the specified database
    public function insert(){
        if(get_class($this) === 'Finder')
            return;
        if(empty($this->fields)){
            echo 'Error - Empty Model Fields';
            return;
        }
        $this->query_string .= 'INSERT INTO ['.get_class($this).'](';
        $this->params = array();
        $fields = '';
        $value_binders = '';
        foreach($this->fields as $key => $value){
            if($key === 'columns' || 
               $key === 'logic' ||
               $key === 'distinct')
                continue;
            $fields .= $key.',';
            $value_binders .= '?,';
            array_push($this->params,$value);
        }
        $fields = rtrim($fields,',').')';
        $value_binders = 'VALUES('.rtrim($value_binders,',').')';
        
        $this->query_string .= $fields.' '.$value_binders;

        $this->prepared_statement = sqlsrv_prepare($this->connectionResource, $this->query_string, $this->params);
        return $this;
    }

    public final function from($table_or_tables = []){
        $this->query_string .= ' FROM ';
        if(!empty($table_or_tables)){
            foreach($table_or_tables as $table_name){
                $this->query_string .= $table_name.',';
            }
            $this->query_string = rtrim($this->query_string,',');
        }
        $this->prepare_query_statement();
        return $this;
    }


    /**
     * Searches for an entry in the database based on this current Model,
     * with $args being the associative array representing the model 
     * fields to be searched.
     */ 
    public function find($args = []){

        $this->query_string .= 'SELECT ';
        if($args !== null){

            // specify distinct data selection
            if(isset($args['distinct']) && $args['distinct'])
                $this->query_string .= 'DISTINCT ';

            // include specific table coulmns whenever specified
            if(isset($args['columns'])){
                foreach($args['columns'] as $col_name){
                    $this->query_string .= $col_name.',';
                }
                $this->query_string = rtrim($this->query_string,',');
            }
            else
                $this->query_string .= '*';
            

            $this->query_string .= ' FROM ['.get_class($this).']';

            // set the relation logic for selecting whenever specified
            
        }
        // echo $this->query_string;
        $this->prepare_query_statement();
        return $this;
    }

    public final function where($args = []){
        if(!empty($args)){
            $logic = isset($args['logic'])? ' '.$args['logic'].' ':' AND ';
            $this->query_string .= ' WHERE ';
            foreach($args as $key => $value){
                if( $key === '' ||
                    $key === 'columns' || 
                    $key === 'logic' || 
                    $key === 'distinct')
                    continue;
                
                /* for the 'IS NULL' and 'IS NOT NULL' sql query */
                if($key === 'NULL' || $key === 'NOT NULL'){
                    $is_null_logic = isset($value['logic'])? $value['logic']:'AND';
                    foreach($value as $key2 => $value2){
                        if($key2 === 'logic')
                            continue;
                        $this->query_string .= '['.$value2.'] IS '.$key.' '.$is_null_logic.' ';
                    }
                    continue;
                }
                /* for the 'LIKE' sql query */
                if($key === 'like'){
                    foreach($value as $k => $v){
                        $this->query_string .= ' '.$k .' LIKE '.$v.' '.$logic.' ';
                    }
                }
                /* for the 'BETWEEN' sql query */
                else if($key === 'between'){
                    $this->query_string .= $value['column'].' BETWEEN '
                                        .$value['arg1'].' AND '
                                        .$value['arg2'].' '
                                        .$logic;
                }
                else{
                    $this->query_string .= $key.' = ?'.$logic;
                    array_push($this->params,$value);
                }
            }
            $this->query_string = rtrim($this->query_string,$logic);
        }
        
        $this->prepare_query_statement();
        return $this;
    }

    public final function delete(){
        $this->query_string .= 'DELETE FROM ['.get_class($this).'] ';
        // $this->prepare_query_statement();
        return $this;
    }

    /// ORDER BY SQL Statement
    public function order_by($args = []){
        if(empty($args))
            return;

        $this->query_string .= ' ORDER BY ';
        foreach($args as $k => $v){
            $this->query_string .=  $k.' '.$v.',';
        }
        $this->query_string = rtrim($this->query_string,',');
        
        $this->prepare_query_statement();
        return $this;
    }

    ///// UPDATE statement here

    public function update($args = []){
        if(empty($args))
            return;

        $this->query_string .= 'UPDATE ['.get_class($this).'] ';
        $set = 'SET ';

        foreach($args as $k => $v){
            array_push($this->params,$v);
            $set .= $k.' = ?,';
        }
        $set = rtrim($set,',').' ';

        $this->query_string .= $set;

        $this->prepare_query_statement();
        return $this;
    }



    protected function prepare_query_statement(){
        $this->prepared_statement = sqlsrv_prepare($this->connectionResource, $this->query_string, $this->params);
        $this->query = sqlsrv_query($this->connectionResource, $this->query_string, $this->params);
    }

    // Performs the query to the Database and closes the connection. This method
    // returns the result set from the performed query.
    public function go(){

        // echo $this->query_string;

        if($this->prepared_statement !== null){
            // Executes a one-time query first.
            if(!sqlsrv_execute($this->prepared_statement)){
                die(print_r(sqlsrv_errors(),true));
            }
        }

        $result_set = [];
   
        if($this->query)
            while($res = sqlsrv_fetch_array($this->query, SQLSRV_FETCH_ASSOC)){
                $model_instance = get_class($this); 
                $result = new $model_instance;
                $result->create($res);
                $result->set_object_source_query($this->query_string);
                array_push($result_set,$result);
            }

        // query string should be refreshed
        $this->query_string = '';
        $this->close();
        return $result_set;
    }
    private function close(){
        unset($this->params);
        $this->params = array();
        sqlsrv_close($this->connectionResource);
        $this->connectionResource = null;
    }

    protected function set_object_source_query($query){
        $this->query_string = $query;
    }
    public function get_query_string(){
        return $this->query_string;
    }

    public function is_match($args = []){
        if($args == null || empty($args))
            return;
        $itemCount = 0;
        $matchCount = 0;

        foreach($args as $key => $value){
            $itemCount++;
            if($this->get($key) === $value){
                $matchCount++;
            }
        }
        return $matchCount>=$itemCount? $this: null;
    }

}