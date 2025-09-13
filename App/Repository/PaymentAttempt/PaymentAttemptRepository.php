<?php

declare(strict_types=1);
namespace App\Repository\PaymentAttempt;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\PaymentAttempt;

class PaymentAttemptRepository implements IPaymentAttemptRepository {
	use Helper;

    /**
     * @var PaymentAttempt[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "payment_attempt";
	private array $columns = [
		"id",
		"payment_id",
		"stripe_payment_intent_id",
		"amount",
		"currency",
		"status",
		"payment_method_id",
		"failure_reason",
		"failure_code",
		"next_action",
		"created_at",
		"updated_at"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findByPaymentId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "payment_id = $id")) return $registers;
		
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
			$this->registers[$v[$this->columns[0]]] = new PaymentAttempt(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(int) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(int) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(string) $v[$this->columns[8]],
				(string) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(string) $v[$this->columns[11]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IPaymentAttemptRepository {

	/**
	 * Summary of findByPaymentId
	 * @param int $id
	 * @return array|PaymentAttempt
	 */
	public function findByPaymentId(int $id);

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
