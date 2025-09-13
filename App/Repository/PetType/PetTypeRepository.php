<?php

declare(strict_types=1);
namespace App\Repository\PetType;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\PetType;

class PetTypeRepository implements IPetTypeRepository {
	use Helper;

    /**
     * @var PetType[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "pet_type";
	private array $columns = [
		"id",
		"category_id",
		"name",
		"description",
		"isActive",
		"created_at",
		"updated_at",
		"deleted_at"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id", "\"isActive\" = TRUE AND deleted_at IS NULL", "", 1)) return $registers;
		
		return $this->getObject($registers);
	}

	public function findAll(array $options = []) : array {
		[$and, $order, $limit, $offset] = $this->database->mountOption($options);
		if(!$registers = $this->database->select("*", $this->table,  "\"isActive\" = TRUE",$and,$order, $limit, $offset)) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByCategoryId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "category_id = $id")) return $registers;
		
		return $this->getObject($registers);
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
			$this->registers[$v[$this->columns[0]]] = new PetType(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(bool) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IPetTypeRepository {
	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|PetType
	 */
	public function findById(int $id);

	/**
	 * Summary of findAll
	 * @param array $options
	 * @return array|PetType
	 */
	public function findAll(array $options = []);

	/**
	 * Summary of findByCategoryId
	 * @param int $id
	 * @return array|PetType
	 */
	public function findByCategoryId(int $id);

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
