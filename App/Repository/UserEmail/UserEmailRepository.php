<?php

declare(strict_types=1);
namespace App\Repository\UserEmail;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\UserEmail;

class UserEmailRepository implements IUserEmailRepository {
	use Helper;

    /**
     * @var UserEmail[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "user_email";
	private array $columns = [
		"id",
		"email",
		"user_id",
		"isPublic",
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

	public function findByEmail(string $email) {
		if(!$registers = $this->database->select("*", $this->table, "email = '$email'")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByUserId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "user_id = $id", '"isActive" = TRUE AND deleted_at IS NULL', "\"isPrimary\", created_at ASC")) return $registers;

		return $this->getObject($registers);
	}

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id", '"isActive" = TRUE AND deleted_at IS NULL', )) return $registers;

		return $this->getObject($registers);
	}

	public function findIsPrimaryByUserId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "user_id = $id", "\"isActive\" = TRUE AND \"isPrimary\" = TRUE AND deleted_at IS NULL")) return $registers;

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
		$data = $this->database->update($this->table, $data, $where, $and);
		return $this->getObject($data);
	}

	public function multipleTransaction(array $matriz): void {
		$this->database->multipleTransaction($matriz);
	}

	private function getObject(array $data){
		foreach ($data as $v){
			$this->registers[$v[$this->columns[0]]] = new UserEmail(
				(int) $v[$this->columns[0]],
				(string) $v[$this->columns[1]],
				(int) $v[$this->columns[2]],
				(bool) $v[$this->columns[3]],
				(bool) $v[$this->columns[4]],
				(bool) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(string) $v[$this->columns[8]]
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IUserEmailRepository {

	/**
	 * Summary of findByEmail
	 * @param string $email
	 * @return array|UserEmail
	 */
	public function findByEmail(string $email);

	/**
	 * Summary of findByUserId
	 * @param int $id
	 * @return array|UserEmail
	 */
	public function findByUserId(int $id);

	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|UserEmail
	 */
	public function findById(int $id);

	/**
	 * Summary of findIsPrimaryByUserId
	 * @param int $id
	 * @return array|UserEmail
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
