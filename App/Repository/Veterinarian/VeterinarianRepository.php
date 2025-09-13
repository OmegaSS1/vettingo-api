<?php

declare(strict_types=1);
namespace App\Repository\Veterinarian;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\Veterinarian;

class VeterinarianRepository implements IVeterinarianRepository {
	use Helper;

    /**
     * @var Veterinarian[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "veterinarian";
	private array $columns = [
		"id",
		"user_id",
		"bio",
		"website",
		"crmv",
		"crmv_state_id",
		"created_at",
		"deleted_at",
		"updated_at",
		"avatar",
		"isActive",
		"emergencial_attendance",
		"domiciliary_attendance",
		"phone_id",
		"professional_email_id",
		"profile_photos"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id", '"isActive" = TRUE AND deleted_at IS NULL')) return $registers;

		return $this->getObject($registers);
	}

	public function findByUserId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "user_id = $id", '"isActive" = TRUE AND deleted_at IS NULL')) return $registers;

		return $this->getObject($registers);
	}
	public function findByCrmv(string $crmv, int $crmv_state_id) {
		if(!$registers = $this->database->select("*", $this->table, "crmv = $crmv", "crmv_state_id = $crmv_state_id AND \"isActive\" = TRUE AND deleted_at IS NULL")) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new Veterinarian(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(int) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(string) $v[$this->columns[8]],
				(string) $v[$this->columns[9]],
				(bool) $v[$this->columns[10]],
				(bool) $v[$this->columns[11]],
				(bool) $v[$this->columns[12]],
				(int) $v[$this->columns[13]],
				(int) $v[$this->columns[14]],
				(string) $v[$this->columns[15]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IVeterinarianRepository {
	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|Veterinarian
	 */
	public function findById(int $id);

	/**
	 * Summary of findByUserId
	 * @param int $id
	 * @return array|Veterinarian
	 */
	public function findByUserId(int $id);

	/**
	 * Summary of findByUserId
	 * @param string $crmv
	 * @param int $crmv_state_id
	 * @return array|Veterinarian
	 */
	public function findByCrmv(string $crmv, int $crmv_state_id);

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
