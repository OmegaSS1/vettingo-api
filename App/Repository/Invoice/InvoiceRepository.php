<?php

declare(strict_types=1);
namespace App\Repository\Invoice;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\Invoice;

class InvoiceRepository implements IInvoiceRepository {
	use Helper;

    /**
     * @var Invoice[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "invoice";
	private array $columns = [
		"id",
		"user_id",
		"subscription_id",
		"stripe_invoice_id",
		"stripe_invoice_number",
		"amount_due",
		"amount_paid",
		"amount_remaining",
		"currency",
		"status",
		"billing_reason",
		"due_date",
		"paid_at",
		"period_start",
		"period_end",
		"subtotal",
		"tax",
		"total",
		"created_at",
		"updated_at"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findBySubscriptionId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "subscription_id = $id")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByStripeInvoiceId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "stripe_invoice_id = $id")) return $registers;
		
		return $this->getObject($registers);
	}

	public function findByUserId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "user_id = $id")) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new Invoice(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(int) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(int) $v[$this->columns[5]],
				(int) $v[$this->columns[6]],
				(int) $v[$this->columns[7]],
				(string) $v[$this->columns[8]],
				(string) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(string) $v[$this->columns[11]],
				(string) $v[$this->columns[12]],
				(string) $v[$this->columns[13]],
				(string) $v[$this->columns[14]],
				(int) $v[$this->columns[15]],
				(int) $v[$this->columns[16]],
				(int) $v[$this->columns[17]],
				(string) $v[$this->columns[18]],
				(string) $v[$this->columns[19]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IInvoiceRepository {

	/**
	 * Summary of findBySubscriptionId
	 * @param int $id
	 * @return array|Invoice
	 */
	public function findBySubscriptionId(int $id);

	/**
	 * Summary of findByStripeInvoiceId
	 * @param int $id
	 * @return array|Invoice
	 */
	public function findByStripeInvoiceId(int $id);

	/**
	 * Summary of findByUserId
	 * @param int $id
	 * @return array|Invoice
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
