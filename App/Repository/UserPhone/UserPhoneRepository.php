<?php

declare(strict_types=1);
namespace App\Repository\UserPhone;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\UserPhone;

class UserPhoneRepository implements IUserPhoneRepository {
	use Helper;

    /**
     * @var UserPhone[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "user_phone";
	private array $columns = [
		"id",
		"user_id",
		"number",
		"areaCode",
		"countryCode",
		"isPublic",
		"isWhatsapp",
		"isActive",
		"isPrimary",
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

	public function findByUserId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "user_id = $id", '"isActive" = TRUE AND deleted_at IS NULL', "\"isPrimary\", created_at ASC")) return $registers;

		return $this->getObject($registers);
	}

	public function findByPhone(string $phone) {
		if(!$registers = $this->database->select("*", $this->table, "number = $phone")) return $registers;

		return $this->getObject($registers);
	}

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id", '"isActive" = TRUE AND deleted_at IS NULL')) return $registers;

		return $this->getObject($registers);
	}

	public function findIsPrimaryByUserId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "user_id = $id", "\"isActive\" = TRUE AND \"isPrimary\" = TRUE AND deleted_at IS NULL")) return $registers;

		return $this->getObject($registers);
	}

	public function insert(array $data): int{
		return $this->database->insert($this->table, $data);
	}

	public function delete(array $data): void{
		$this->database->delete($this->table, $data);
	}

	public function update(array $data, string $where, string $and = ""): void{
		$this->database->update($this->table, $data, $where, $and);
	}

	public function multipleTransaction(array $matriz): void {
		$this->database->multipleTransaction($matriz);
	}

	private function getObject(array $data){
		foreach ($data as $v){
			$this->registers[$v[$this->columns[0]]] = new UserPhone(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(bool) $v[$this->columns[5]],
				(bool) $v[$this->columns[6]],
				(bool) $v[$this->columns[7]],
				(bool) $v[$this->columns[8]],
				(string) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(string) $v[$this->columns[11]]
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IUserPhoneRepository {
	/**
	 * Summary of findByUserId
	 * @param int $id
	 * @return array|UserPhone
	 */
	public function findByUserId(int $id);

	/**
	 * Summary of findByPhone
	 * @param string $phone
	 * @return array|UserPhone
	 */
	public function findByPhone(string $phone);

	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|UserPhone
	 */
	public function findById(int $id);
	
	/**
	 * Summary of findIsPrimaryByUserId
	 * @param int $id
	 * @return array|UserPhone
	 */
	public function findIsPrimaryByUserId(int $id);
	
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
