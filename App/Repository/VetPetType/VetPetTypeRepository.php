<?php

declare(strict_types=1);
namespace App\Repository\VetPetType;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\VetPetType;

class VetPetTypeRepository implements IVetPetTypeRepository {
	use Helper;

    /**
     * @var VetPetType[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "vet_pet_type";
	private array $columns = [
		"id",
		"veterinarian_id",
		"pet_type_id",
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

	public function findByVeterinarianId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "veterinarian_id = $id")) return $registers;

		return $this->getObject($registers);
	}

	public function findByPetTypeId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "pet_type_id = $id")) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new VetPetType(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(int) $v[$this->columns[2]],
				(bool) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IVetPetTypeRepository {
	/**
	 * Summary of findByVeterinarianId
	 * @param int $id
	 * @return array|VetPetType
	 */
	public function findByVeterinarianId(int $id);

	/**
	 * Summary of findByPetTypeId
	 * @param int $id
	 * @return array|VetPetType
	 */
	public function findByPetTypeId(int $id);

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
