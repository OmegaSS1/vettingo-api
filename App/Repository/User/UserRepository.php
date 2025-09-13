<?php

declare(strict_types=1);
namespace App\Repository\User;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\User;

class UserRepository implements IUserRepository {
	use Helper;

    /**
     * @var User[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "public.user";
	private array $columns = [
		"id",
		"isActive",
		"role",
		"cpf",
		"created_at",
		"updated_at",
		"deleted_at",
		"first_name",
		"last_name",
		"gender",
		"birth_date",
		"wants_newsletter",
		"avatar",
		"stripe_customer_id"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findByCpf(string $value) {
		if(!$registers = $this->database->select("*", $this->table, "cpf = '$value'")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByStripeCustomerId(string $value) {
		if(!$registers = $this->database->select("*", $this->table, "stripe_customer_id = '$value'")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id")) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new User(
				(int) $v[$this->columns[0]],
				(bool) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(string) $v[$this->columns[8]],
				(string) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(bool) $v[$this->columns[11]],
				(string) $v[$this->columns[12]],
				(string) $v[$this->columns[13]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IUserRepository {

	/**
	 * Summary of findByCpf
	 * @param string $value
	 * @return array|User
	 */
	public function findByCpf(string $value);

	/**
	 * Summary of findByStripeCustomerId
	 * @param string $value
	 * @return array|User
	 */
	public function findByStripeCustomerId(string $value);

	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|User
	 */
	public function findById(int $id);

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
