<?php

namespace App\Model;

use App\Traits\Helper;
use Exception;
use \PDO;
use \PDOException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class Database{

    use Helper;

    const DB_HOST = ENV['HOST'];
    const DB_PORT = ENV['PORT'];
    const DB_NAME = ENV['DBNAME'];
    const DB_USER = ENV['USERNAME'];
    const DB_PASSWORD = ENV['PASSWORD'];
    const DEVELOPMENT = ENV['DEVELOPMENT'];

    public bool $commit = true;
    public $connection = null;
    private $allowedTables = null;
    private string $loggerMessage = "Erro inesperado na validação dos dados! Consulte o administrador do sistema.";
    private LoggerInterface $loggerInterface;

    public function __construct(LoggerInterface $loggerInterface){
        $this->loggerInterface = $loggerInterface;
        try {

            if ($this->connection === null ){
                $this->connection = new PDO("pgsql:host=".self::DB_HOST.";port=".self::DB_PORT.";dbname=".self::DB_NAME, self::DB_USER, self::DB_PASSWORD);
                $this->connection->exec("SET NAMES 'UTF8'");
                $this->getTables();
            }

            if (!self::DEVELOPMENT)
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            else 
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            $this->loggerInterface->error("DATABASE (GETCOLUMNMETA)", ["message" => $e->getMessage(), "code" => $e->getCode(), "file" => $e->getFile(), "line" => $e->getLine()]);
            throw new Exception($this->loggerMessage);
        }
    }

    private function getTables(): void{
        $stm = $this->connection->prepare("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname NOT IN ('pg_catalog', 'information_schema')");
        $stm->execute();
        $tables = $stm->fetchAll(PDO::FETCH_COLUMN);

        $this->allowedTables = $tables;
    }

    private function validTable(string $table){
        //$table = explode(' ', $table);
        $table = str_replace("public.", "", $table);
        if(!in_array($table, $this->allowedTables)){
            throw new Exception($this->loggerMessage);
        }
    }

    /**
     * RETORNA A CONSULTA NO BANCO (PARA QUERIES MAIS COMPLEXAS E EXTENSAS)
     * @var string
     * @return array
     */
    public function getColumnMeta(string $table)
    {
        try {
            $this->validTable($table);
            ## Pega o tamanho maximo permitido de cada coluna
            $l = "SELECT character_maximum_length FROM information_schema.columns WHERE table_name = '$table'";
            $stm = $this->connection->prepare($l);
            $stm->execute();
            $l = $stm->fetchAll(PDO::FETCH_ASSOC);

            ## Pega a estrutura de cada coluna
            $m = "SELECT * FROM $table";
            $stm = $this->connection->prepare($m);
            $stm->execute();

            $countColumn = $stm->columnCount();
            for ($i = 0; $i < $countColumn; $i++) {
                $meta[] = $stm->getColumnMeta($i);
                $meta[$i]['length'] = @$l[$i]['character_maximum_length'];
            };
            return $meta;
        } catch (Exception $e) {
            $this->loggerInterface->error("DATABASE (GETCOLUMNMETA)", ["message" => $e->getMessage(), "code" => $e->getCode(), "file" => $e->getFile(), "line" => $e->getLine()]);
            throw new Exception($this->loggerMessage);
        }
    }


    public function runQuery($sql){
        $stm = $this->connection->prepare($sql);
        $stm->execute();
        return $stm;
    }
    
    public function runSelect($sql): array{
        $stm = $this->connection->prepare($sql);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function runRow($sql): int{
        $stm = $this->connection->prepare($sql);
        $stm->execute();
        return $stm->rowCount();
    } 
    
    public function row($select, $table, $where = '', $and = '', $order = '', $limit = ''): int{
        $where = strlen($where) ? 'WHERE '   .$where : '';
        $and   = strlen($and)   ? 'AND '     .$and   : '';
        $order = strlen($order) ? 'ORDER BY '.$order : '';
        $limit = strlen($limit) ? 'LIMIT '   .$limit : '';

        $query = "SELECT $select FROM $table $where $and $order $limit";

        try{
            $this->validTable($table);

            $stm = $this->connection->prepare($query);
            $stm->execute();
            return $stm->rowCount();

        }catch(PDOException $e){
            $this->connection = null;
            $this->loggerInterface->info('(DATABASE ROW)', array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine()));
            throw new Exception($this->loggerMessage, 400);
        }
    }
    
    public function select(string $select, string $table, string $where = '', string $and = '', string $order = '', int $limit = 0, int $offset = 0): array{
        $paramWhere = strlen($where) ? "WHERE $where" : "";
        $paramAnd   = strlen($and)   ? "AND $and" : "";
        $paramOrder = strlen($order) ? "ORDER BY $order" : "";
        $paramLimit = $limit > 0 ? "LIMIT ? " : "";
        $paramOffset = $offset > 0 ? "OFFSET ? " : "";

        $bindParams = [];
        $data1 = $this->toBind($paramWhere, "WHERE");
        $data2 = $this->toBind($paramAnd, "AND");

        if(isset($data1[0]) and isset($data1[1])){
            $paramWhere = $data1[0];
            if($data1[1] !== null or !empty($data1[1])) $bindParams = array_merge($bindParams, $data1[1]);
        }
        if(isset($data2[0]) and isset($data2[1])){
            $paramAnd = $data2[0];
            if($data2[1] !== null or !empty($data2[1])) $bindParams = array_merge($bindParams, $data2[1]);
        }
        
        if(!empty($paramLimit)) $bindParams[] = $limit;
        if(!empty($paramOffset)) $bindParams[] = $offset;

        $query = "SELECT $select FROM $table $paramWhere $paramAnd $paramOrder $paramLimit $paramOffset";
        try{
            $this->validTable($table);

            $stm = $this->connection->prepare($query);
            $stm->execute(array_values($bindParams));
            return $stm->fetchAll(PDO::FETCH_ASSOC);

        }catch(PDOException $e){
            $this->connection = null;
            $this->loggerInterface->info('(DATABASE SELECT)', array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine()));
            throw new Exception($this->loggerMessage, 400);
        }
    }
    
    public function update($table, $values, $where, $and = ''){
        $and    = strlen($and) ? ' AND '.$and : '';
        $fields = array_keys($values);

        $query  = "UPDATE $table SET ".implode(" = ?, ", $fields)." = ? WHERE $where $and RETURNING *";

        try {
            $this->validTable($table);

            #Verifica se esta logado
            $stm = $this->bindValue($query, $values);
            return $stm->fetchAll(PDO::FETCH_ASSOC);

        }catch(Exception $e){
            $this->connection->rollback();
            $this->connection = null;
            $this->loggerInterface->info('(DATABASE UPDATE)', array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine()));
            throw new Exception($this->loggerMessage, 400);
        }
    }
    
    public function insert($table, $values){

        $fields = array_keys($values);
        $binds  = array_pad([], count($fields), '?');
        $query  = "INSERT INTO $table (".implode(',', $fields).") VALUES(".implode(',', $binds).") RETURNING *";

        try {
            $this->validTable($table);

            #Verifica se esta logado
            $stm = $this->bindValue($query, $values);
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            $this->connection->rollback();
            $this->connection = null;
            $this->loggerInterface->info('(DATABASE INSERT)', array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine()));
            throw new Exception($this->loggerMessage, 400);
        }
    }
    
    public function delete($table, $values): void{
        $fields = array_keys($values);
        $query = "DELETE FROM $table WHERE ".implode(" = ? AND ", $fields)." = ?";

        try {
            $this->validTable($table);

            #Verifica se esta logado
            $this->bindValue($query, $values);

        }catch(PDOException $e){
            $this->connection->rollback();
            $this->connection = null;
            $this->loggerInterface->info('(DATABASE DELETE)', array('message' => $e->getMessage(), 'code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine()));
            throw new Exception($this->loggerMessage, 400);
        }
    }

    private function bindValue($query, array $values) {
        if (!$this->connection->inTransaction())
            $this->connection->beginTransaction();

        $stm = $this->connection->prepare($query);
        $stm->execute(array_values($values));

        if ($this->commit){
            $this->connection->commit();
        }

        return $stm;
    }

    public function mountOption(array $options = [], string $flag = ""){
        $orderParams = "";
        $and = "";
        $limit = 0;
        $offset = 0;
		foreach($options as $key => $value) {
            if(is_array($value)){
                $column = $value["column"] ?? "";
                $value = $value["value"];
            }
			switch($key) {
				case "active":
					$value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
					$and .= "\"isActive\" = " . ($value === false ? 'FALSE' : 'TRUE');
					break;
                case "search":
                    if(!$column) break;
					!strlen($value) ? "" : $and .= " AND $column = '$value'";
					break;
				case "stateId":
                    !strlen($value) ? "" : $and .= " AND state_id = $value";
                    break;
				case "cityId":
					!strlen($value) ? "" : $and .= " AND city_id = $value";
					break;
                case "rating":
					!strlen($value) ? "" : $and .= " AND rating = $value";
					break;
                case "minRating":
					!strlen($value) ? "" : $and .= " AND rating >= $value";
					break;
                case "startDate":
					!$this->validateTime($value) ? "" : $and .= " AND created_at >= $value";
					break;
                case "endDate":
					!$this->validateTime($value) ? "" : $and .= " AND created_at <= $value";
					break;
                case "anonymous":
					$and .= " AND anonymous = " . ($value === false ? "FALSE" : "TRUE");
					break;
				case "orderBy":
					switch($value) {
						case "name":
							$orderParams = "name";
							break;
						case "createdAt":
							$orderParams = "created_at";
							break;
						case "deletedAt":
							$orderParams = "deleted_at";
                            break;
                        case "dayOfWeek":
                            $orderParams = "day_of_week";
					}
					break;
				case "orderDirection":
					$value = (string) strtoupper($value);
					$orderParams .= !strlen($orderParams) ? "" : (in_array($value, ["ASC", "DESC"]) ? " $value" : " ASC");
					break;
                case "pageSize":
					$limit = (int) $value;
					break;
				case "page":
					$offset = $value == 0 ? 0 : ((int) $value - 1) * $limit;
					break;
			}
		}

        if (str_starts_with($and, " AND ")) {
            $and = substr($and, strlen(" AND "));
        }
        return [$and, $orderParams, $limit, $offset];
    }

    public function multipleTransaction(array $matriz): void{
        try {
            foreach($matriz as $sequence){
                [$table, $values, $where, $and, $typeTransaction] = [$sequence['table'], $sequence['value'], $sequence['where'], !isset($sequence['and']) ? '' : $sequence['and'], $sequence['typeTransaction']];

                switch($typeTransaction){
                    case "INSERT":
                        $fields = array_keys($values);
                        $binds  = array_pad([], count($fields), '?');
                        $query  = "INSERT INTO $table (".implode(',', $fields).") VALUES(".implode(',', $binds).")";
                        break;

                    case "UPDATE":
                        $and    = strlen($and) ? " AND $and" : '';
                        $fields = array_keys($values);
                        $query  = "UPDATE $table SET ".implode(" = ?, ", $fields)." = ? WHERE $where $and";
                        break;
                    
                    case "DELETE":
                        $fields = array_keys($values);
                        $query = "DELETE FROM $table WHERE ".implode(" = ? AND ", $fields)." = ?";
                        break;
                        
                    default:
                        throw new Exception($this->loggerMessage);
                }

                $this->validTable($table);

                $stm = $this->connection->prepare($query);

                if (!$this->connection->inTransaction())
                    $this->connection->beginTransaction();

                $stm->execute(array_values($values));
            }

            $this->connection->commit();
        } catch (Exception $e){
            $this->connection->rollBack();
            $this->connection = null;
            $this->loggerInterface->error("DATABASE (MULTIPLETRANSACTION)", ["message" => $e->getMessage(), "code" => $e->getCode(), "file" => $e->getFile(), "line" => $e->getLine()]);
            throw new Exception($this->loggerMessage, 400);
        }
    }

    private function toBind(string $data, string $type = "WHERE"){
        if(empty($data)) return [];
        $toArr = array_filter(explode($type, $data));
        $data = "";
        $param = [];

        foreach($toArr as $v){
            $arr = explode("=", $v);
            
            $column = trim($arr[0]);
            if(isset($arr[1]) and !empty($arr[1])){
                $value = trim($arr[1]);
                $value = (string) str_replace("'", "", $value);
                $data .= " $type $column = ?";
                array_push($param, $value);
            }
            else {
                $data .= " $type $column";
            }
        }

        return [$data, $param];
    }
}
    
    
