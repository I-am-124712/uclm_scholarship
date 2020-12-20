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
    protected $query_method;


    // Readies the connection to the SQLServer DB
    public function ready(){
        // query string should be refreshed
        $this->query_string = '';

        if($this->connectionResource === null)
            $this->connectionResource = sqlsrv_connect($this->server,$this->connectionInfo);
        return $this;
    }

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
            return $this->fields[$key];
        }
    }

    
    // Performs an INSERT operation into the specified database
    public function insert(){
        $this->query_method = 'DML';

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
            if($value === 'CURRENT_TIMESTAMP'){
                $value_binders .= 'CURRENT_TIMESTAMP,';
                continue;
            }
            else
                $value_binders .= '?,';
            array_push($this->params,$value);
        }
        $fields = rtrim($fields,',').')';
        $value_binders = 'VALUES(' . rtrim($value_binders,',') . ')';
        
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
        return $this;
    }


    /**
     * Searches for an entry in the database based on this current Model,
     * with $args being the associative array representing the model 
     * fields to be searched.
     */ 
    public function find($args = []){
        $this->query_method = 'DQL';

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
        
        return $this;
    }

    public final function delete(){
        $this->query_method = 'DML';
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
        
        return $this;
    }

    ///// UPDATE statement here

    public function update($args = []){
        $this->query_method = 'DML';

        if(empty($args))
            return;

        $this->query_string .= 'UPDATE ['.get_class($this).'] ';
        $set = 'SET ';

        foreach($args as $k => $v){
            if($this->check_null($v)){
                $set .= '['.$k.'] = NULL,';
            }else if($v === 'CURRENT_TIMESTAMP'){
                $set .= '[' . $k . '] = CURRENT_TIMESTAMP,';
            }
            else{
                array_push($this->params, $v);
                $set .= '['.$k.'] = ?,';
            }
        }
        $set = rtrim($set,',').' ';

        $this->query_string .= $set;

        return $this;
    }



    public final function prepare_query_statement(){
        $this->prepared_statement = sqlsrv_prepare($this->connectionResource, $this->query_string, $this->params);
        $yes = $this->query = sqlsrv_query($this->connectionResource, $this->query_string, $this->params);
        
        return $yes;
    }

    // Performs the query to the Database and closes the connection. This method
    // returns the result set from the performed query.
    public function go(){

        // echo $this->query_string;
        
        if($this->query_method === 'DML' || $this->query_method === 'Custom'){
            // Perform a DML
            if($this->prepare_query_statement()){
                $this->close();
                return true;
            }
            else{
                return false;
            }
        }
        else{
            $this->prepare_query_statement();
            $result_set = [];
    
            if($this->query){
                while($res = sqlsrv_fetch_array($this->query, SQLSRV_FETCH_ASSOC)){
                    $model_instance = get_class($this); 
                    $result = new $model_instance;
                    $result->create($res);
                    $result->set_object_source_query($this->query_string);
                    array_push($result_set,$result);
                }
                $this->close();
                return $result_set;
            }
        }
    }

    /**
     * Executes a non-query statement without closing the connection and clearing the saved SQL command and parameter binds.
     */
    public final function executeNonQuery(){
        
        if($this->query_method === 'DML' || $this->query_method === 'Custom'){
            // Perform a DML
            if(sqlsrv_query($this->connectionResource, $this->query_string, $this->params)){
                return true;
            }
            else{
                return false;
            }
        }
    }

    /**
    *    Performs a normal Data Query Operation using this Model object (SQL SELECT).
    *    This will be the last method that should be chained when doing a select() or find()
    *    method chaining. (Note: Once you specify the 'index' argument, the ranged selection options 
    *    (start_index & end_index) will be overridden, and this method will proceed to return 
    *    only the element this argument specifies.)

    *    Params:
    *    - $options [] = Associative array containing the options on what to retrieve. If 
    *        empty, this method returns the result set itself.
    *            
    *    $options:
    *    - "index" => int = the index of a single item from the result set to retrieve.
    *        
    *    - "start_index" => int = starting index of the result set items to retrieve.
    *    - ["end_index" => int] = (optional) end index of the result set items to retrieve.
    *       If unset, end index will be result set length. 
     */
    public function result_set($options = []){

        $this->prepare_query_statement();
        $result_set = [];

        if($this->query){
            while($res = sqlsrv_fetch_array($this->query, SQLSRV_FETCH_ASSOC)){
                $model_instance = get_class($this); 
                $result = new $model_instance;
                $result->create($res);
                $result->set_object_source_query($this->query_string);
                array_push($result_set,$result);
            }
            $this->close();
        }

        if(!empty($options)){
            if(isset($options['index'])){
                $index = max(0,$options['index']);
                return isset($result_set[$index])? $result_set[$index]:null;
            }
            else{
                $start_index = isset($options['start_index'])? $options['start_index']: 0;
                $end_index = isset($options['end_index'])? $options['end_index']:-1;
    
                $new_result_set = [];
    
                // We return the item specified by the start_index whenever the end_index
                // is lesser than the start index. We don't want to overbound our array;
                if($end_index <= $start_index){
                    return isset($result_set[$start_index])? $result_set[$start_index]:null;
                }else{

                    // normalize bounds
                    $start_index = min((count($result_set)-1), $start_index);
                    $end_index = min((count($result_set)-1), $end_index);

                    // fill our return set
                    for($i=0; $current_i = $start_index++ < $end_index; ++$i){
                        $new_result_set[$i] = $result_set[$current_i];
                    }

                    $result_set = $new_result_set;
                }
            }
            $this->close();
        }
        
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
        echo $this->query_string;
        return $this;
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


    private function check_null($v){
       return $v === null ||
        $v === 'null' ||
        $v === 'nulL' ||
        $v === 'nuLl' ||
        $v === 'nuLL' ||
        $v === 'nUll' ||
        $v === 'nUlL' ||
        $v === 'nULl' ||    // bruh
        $v === 'nULL' ||
        $v === 'Null' ||
        $v === 'NulL' ||
        $v === 'NuLl' ||
        $v === 'NuLL' ||
        $v === 'NUll' ||
        $v === 'NUlL' ||
        $v === 'NULl' ||
        $v === 'NULL';
    }
}