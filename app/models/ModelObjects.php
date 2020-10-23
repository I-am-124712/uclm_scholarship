<?php 
require_once './app/core/Model.php';

final class User extends Model{}
final class UserPrivilege extends Model{}
final class Departments extends Model{}
final class WS extends Model{}
final class Schedule extends Model{}
final class Records extends Model{}
final class Overtime extends Model{}

/* Special Finder class for Complex Queries */
final class Finder extends Model {

    public final function select($cols){
        if(!empty($cols)){
            $this->query_string .= 'SELECT ';
            foreach($cols as $column){
                $this->query_string .= $column.',';
            }
            $this->query_string = rtrim($this->query_string,',');
        }
        $this->prepare_query_statement();
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

    public final function join($args = []){
        $table = '';
        $logic = '';
        $conditions = '';


        if(!empty($args)){
            $join_type = isset($args['join-type'])? $args['join-type']:'';

            if(isset($args['table'])){
                $table = $args['table'];
            }
            else return;

            if(isset($args['on']) && !empty($args['on'])){
                $logic = ' '.(isset($args['on']['logic']) ? $args['on']['logic']:'AND').' ';
                if(isset($args['on']['conditions'])){
                    foreach($args['on']['conditions'] as $conds){
                        $conditions .= $conds.$logic;
                    }
                    $conditions = rtrim($conditions,$logic);
                }
            }
            else return;

            $this->query_string .= ' '.$join_type.' JOIN '.$table.' ON '.$conditions;
        }
        $this->prepare_query_statement();
        return $this;
    }

    public final function group($cols = []){
        if(empty($cols))
            return;
        $columns='';
        foreach($cols as $column){
            $columns .= $column.',';
        }
        $columns = rtrim($columns,',');
        $this->query_string .= ' GROUP BY '.$columns;
        return $this;
    }

    public final function having($conds = []){
        if(empty($conds))
            return;
        $conditions = '';
        $logic = isset($conds['logic'])? $conds['logic']:'AND'; 


        foreach($conds as $k => $c){
            if($k === 'logic')
                continue;
            $conditions .= $c.' '.$logic.' ';
        }
        $conditions = rtrim($conditions,' '.$logic.' ');
    
        $this->query_string .= ' HAVING '.$conditions;
        $this->prepare_query_statement();
        return $this;
    }

}