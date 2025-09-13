<?php

declare(strict_types=1);
namespace App\Repository\City;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\City;

class CityRepository implements ICityRepository {
	use Helper;

    /**
     * @var City[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "city";
	private array $columns = [
		"id",
		"name",
		"state_id",
		"isActive",
		"created_at",
		"updated_at"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findByStateId(int $id, array $options = []) {
		[$and, $orderParams, $limit, $offset] = $this->database->mountOption($options);

		if(!$registers = $this->database->select("*", $this->table, "state_id = $id", $and, $orderParams, $limit, $offset)) return $registers;
		
		return $this->getObject($registers);
	}

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id")) return $registers;

		return $this->getObject($registers);
	}

	public function findTotalByStateId(int $id) {
		return $this->database->row("*", $this->table, "state_id = $id");
	}

	public function findTotalActiveInactiveByState(int $id){
		$query = <<<SQL
			SELECT COUNT(*) FROM city  WHERE state_id = $id  AND "isActive" = 'TRUE'
			UNION ALL
			SELECT COUNT(*) FROM city  WHERE state_id = $id  AND "isActive" = 'FALSE'
		SQL;

		return $this->database->runSelect($query);
	}

	public function insert(array $data){
		$data = $this->database->insert($this->table, $data);
		return $this->getObject($data);
	}

	public function delete(array $data): void{
		$this->database->delete($this->table, $data);
	}

	public function update(array $data, string $where, string $and = ""){
		$data = $this->database->update($this->table, $data, $where,$and);
		return $this->getObject($data);
	}

	public function multipleTransaction(array $matriz): void {
		$this->database->multipleTransaction($matriz);
	}

	private function getObject(array $data){
		foreach ($data as $v){
			$this->registers[$v[$this->columns[0]]] = new City(
				(int) $v[$this->columns[0]],
				(string) $v[$this->columns[1]],
				(int) $v[$this->columns[2]],
				(bool) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface ICityRepository {

	/**
	 * Summary of findByStateId
	 * @param int $id
	 * @param array $options
	 * @return array|City
	 */
	public function findByStateId(int $id, array $options = []);

	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|City
	 */
	public function findById(int $id);

	/**
	 * Summary of findTotalByStateId
	 * @param int $id
	 * @return int
	 */
	public function findTotalByStateId(int $id);

	/**
	 * Summary of findTotalActiveInactiveByState
	 * @param int $id
	 * @return array
	 */
	public function findTotalActiveInactiveByState(int $id);

	/**
	 * Summary of insert
	 * @param array $user
	 * @return mixed
	 */
	public function insert(array $user);

	/**
	 * Summary of update
	 * @param array $data
	 * @param string $where
	 * @param string $and
	 * @return mixed
	 */
	public function update(array $data, string $where, string $and = "");

	/**
	 * Summary of delete
	 * @param array $data
	 * @return mixed
	 */
	public function delete(array $data);

	/**
	 * Summary of multipleTransaction
	 * @param array $matriz
	 * @return void
	 */
	public function multipleTransaction(array $matriz);
}
