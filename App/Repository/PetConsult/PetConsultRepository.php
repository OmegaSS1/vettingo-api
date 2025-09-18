<?php

declare(strict_types=1);
namespace App\Repository\PetConsult;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\PetConsult;

class PetConsultRepository implements IPetConsultRepository {
	use Helper;

    /**
     * @var PetConsult[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "pet_consult";
	private array $columns = [
		"id",
		"pet_id",
		"tutor_name",
		"pet_name",
		"vet_id",
		"consultation_date",
		"reason",
		"diagnosis",
		"treatment",
		"prescription",
		"notes",
		"weight",
		"temperature",
		"status",
		"created_at",
		"updated_at"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByConsultationDate(string $date) {
		if(!$registers = $this->database->select("*", $this->table, "DATE(consultation_date) = $date")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByPetId(int $id, array $options = []) {
		[$and, $order, $limit, $offset] = $this->database->mountOption($options);
		if(!$registers = $this->database->select("*", $this->table, "pet_id = $id", $and, $order, $limit, $offset)) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByVetId(int $id, array $options = []) {
		[$and, $order, $limit, $offset] = $this->database->mountOption($options);
		if(!$registers = $this->database->select("*", $this->table, "vet_id = $id", $and, $order, $limit, $offset)) return $registers;
		
		return $this->getObject($registers);
	}

	public function findTotal(int $id) {
		return $this->database->runRow("SELECT id FROM {$this->table} WHERE pet_id = $id");
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
			$this->registers[$v[$this->columns[0]]] = new PetConsult(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(int) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(string) $v[$this->columns[8]],
				(string) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(int) $v[$this->columns[11]],
				(int) $v[$this->columns[12]],
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

interface IPetConsultRepository {
	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|PetConsult
	 */
	public function findById(int $id);

	/**
	 * Summary of findByConsultationDate
	 * @param string $date
	 * @return array|PetConsult
	 */
	public function findByConsultationDate(string $date);

	/**
	 * Summary of findByPetId
	 * @param int $id
	 * @param array $options
	 * @return array|PetConsult
	 */
	public function findByPetId(int $id, array $options = []);

	/**
	 * Summary of findByVetId
	 * @param int $id
	 * @param array $options
	 * @return array|PetConsult
	 */
	public function findByVetId(int $id, array $options = []);
	
	/**
	 * Summary of findTotal
	 * @param int $id
	 * @return int
	 */
	public function findTotal(int $id);

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
