<?php

declare(strict_types=1);
namespace App\Repository\Payment;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\Payment;

class PaymentRepository implements IPaymentRepository {
	use Helper;

    /**
     * @var Payment[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "payment";
	private array $columns = [
		"id",
		"user_id",
		"subscription_id",
		"stripe_payment_intent_id",
		"stripe_invoice_id",
		"amount",
		"currency",
		"status",
		"description",
		"metadata",
		"paid_at",
		"failed_at",
		"refunded_at",
		"refund_amount",
		"created_at",
		"updated_at",
		"stripe_checkout_id"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findByStripeCheckoutId(string $id) {
		if(!$registers = $this->database->select("*", $this->table, "stripe_checkout_id = '$id'")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByUserId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "user_id = $id")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findBySubscriptionId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "subscription_id = $id")) return $registers;

		return $this->getObject($registers);
	}

	public function findByStripeInvoiceId(string $id) {
		if(!$registers = $this->database->select("*", $this->table, "stripe_invoice_id = '$id'", "", "", 1)) return $registers;

		return $this->getObject($registers);
	}

	public function findByStripePaymentIntentId(string $id) {
		if(!$registers = $this->database->select("*", $this->table, "stripe_payment_intent_id = '$id'", "", "", 1)) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new Payment(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(int) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(int) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(string) $v[$this->columns[8]],
				(string) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(string) $v[$this->columns[11]],
				(string) $v[$this->columns[12]],
				(int) $v[$this->columns[13]],
				(string) $v[$this->columns[14]],
				(string) $v[$this->columns[15]],
				(string) $v[$this->columns[16]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IPaymentRepository {
	/**
	 * Summary of findByUserId
	 * @param int $id
	 * @return array|Payment
	 */
	public function findByUserId(int $id);

	/**
	 * Summary of findByStripeCheckoutId
	 * @param string $id
	 * @return array|Payment
	 */
	public function findByStripeCheckoutId(string $id);

	/**
	 * Summary of findBySubscriptionId
	 * @param int $id
	 * @return array|Payment
	 */
	public function findBySubscriptionId(int $id);

	/**
	 * Summary of findByStripeInvoiceId
	 * @param string $id
	 * @return array|Payment
	 */
	public function findByStripeInvoiceId(string $id);

	/**
	 * Summary of findByStripePaymentIntentId
	 * @param string $id
	 * @return array|Payment
	 */
	public function findByStripePaymentIntentId(string $id);

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
