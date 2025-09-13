<?php

declare(strict_types=1);
namespace App\Repository\Pet;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\Pet;

class PetRepository implements IPetRepository {
	use Helper;

    /**
     * @var Pet[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "pet";
	private array $columns = [
		"id",
		"name",
		"owner_id",
		"pet_type_id",
		"breed",
		"birth_date",
		"gender",
		"weight",
		"has_pedigree",
		"pedigree_number",
		"avatar",
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
		if(!$registers = $this->database->select("*", $this->table, "id = $id", "\"isActive\" = TRUE AND deleted_at IS NULL")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByOwnerId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "owner_id = $id", "\"isActive\" = TRUE AND deleted_at IS NULL")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByPedigreeNumber(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "pedigree_number = $id", "\"isActive\" = TRUE")) return $registers;
		
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
		$data = $data = $this->database->update($this->table, $data, $where,$and);
		return $this->getObject($data);
	}

	public function multipleTransaction(array $matriz): void {
		$this->database->multipleTransaction($matriz);
	}

	private function getObject(array $data){
		foreach ($data as $v){
			$this->registers[$v[$this->columns[0]]] = new Pet(
				(int) $v[$this->columns[0]],
				(string) $v[$this->columns[1]],
				(int) $v[$this->columns[2]],
				(int) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(bool) $v[$this->columns[8]],
				(string) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(string) $v[$this->columns[11]],
				(bool) $v[$this->columns[12]],
				(string) $v[$this->columns[13]],
				(string) $v[$this->columns[14]],
				(string) $v[$this->columns[15]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IPetRepository {
	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|Pet
	 */
	public function findById(int $id);

	/**
	 * Summary of findByOwnerId
	 * @param int $id
	 * @return array|Pet
	 */
	public function findByOwnerId(int $id);

	/**
	 * Summary of findByPedigreeNumber
	 * @param int $id
	 * @return array|Pet
	 */
	public function findByPedigreeNumber(int $id);

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
