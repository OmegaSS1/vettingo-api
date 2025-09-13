<?php

declare(strict_types=1);
namespace App\Repository\UserBillingInfo;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\UserBillingInfo;

class UserBillingInfoRepository implements IUserBillingInfoRepository {
	use Helper;

    /**
     * @var UserBillingInfo[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "user_billing_info";
	private array $columns = [
		"id",
		"user_id",
		"full_name",
		"street",
		"number",
		"complement",
		"neighborhood",
		"zip_code",
		"city_id",
		"state_id",
		"strip_customer_id",
		"is_active",
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
		if(!$registers = $this->database->select("*", $this->table, "user_id = $id")) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new UserBillingInfo(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(int) $v[$this->columns[8]],
				(int) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(bool) $v[$this->columns[11]],
				(string) $v[$this->columns[12]],
				(string) $v[$this->columns[13]],
				(string) $v[$this->columns[14]]
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IUserBillingInfoRepository {

	/**
	 * Summary of findProfissionalById
	 * @param int $id
	 * @return array|UserBillingInfo
	 */
	public function findByUserId(int $id);

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
