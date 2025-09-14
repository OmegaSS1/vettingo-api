<?php

declare(strict_types=1);
namespace App\Repository\PetDocument;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\PetDocument;

class PetDocumentRepository implements IPetDocumentRepository {
	use Helper;

    /**
     * @var PetDocument[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "pet_document";
	private array $columns = [
		"id",
		"pet_id",
		"title",
		"document",
		"document_length",
		"created_at",
		"deleted_at"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id", "deleted_at IS NULL")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByPetId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "pet_id = $id", "deleted_at IS NULL")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findTotal(int $id) {
		return $this->database->runRow("SELECT id FROM {$this->table} WHERE pet_id = $id AND deleted_at IS NULL");
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
			$this->registers[$v[$this->columns[0]]] = new PetDocument(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
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

interface IPetDocumentRepository {
	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|PetDocument
	 */
	public function findById(int $id);

	/**
	 * Summary of findByPetId
	 * @param int $id
	 * @return array|PetDocument
	 */
	public function findByPetId(int $id);

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
