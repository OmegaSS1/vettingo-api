<?php

declare(strict_types=1);
namespace App\Repository\PhoneVerification;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\PhoneVerification;

class PhoneVerificationRepository implements IPhoneVerificationRepository {
	use Helper;

    /**
     * @var PhoneVerification[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "phone_verification";
	private array $columns = [
		"id",
		"phone_id",
		"token",
		"created_at",
		"updated_at",
		"expired_at",
		"completed_at"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findByPhoneId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "phone_id = $id")) return $registers;
		
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
			$this->registers[$v[$this->columns[0]]] = new PhoneVerification(
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

interface IPhoneVerificationRepository {

	/**
	 * Summary of findByPhoneId
	 * @param int $id
	 * @return array|PhoneVerification
	 */
	public function findByPhoneId(int $id);

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
