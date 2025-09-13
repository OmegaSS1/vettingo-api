<?php

declare(strict_types=1);
namespace App\Repository\VeterinarianApprovalPending;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\VeterinarianApprovalPending;

class VeterinarianApprovalPendingRepository implements IVeterinarianApprovalPendingRepository {
	use Helper;

    /**
     * @var VeterinarianApprovalPending[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "veterinarian_approval_pending";
	private array $columns = [
		"id",
		"veterinarian_id",
		"rg_front_image_url",
		"rg_back_image_url",
		"crmv_document_image_url",
		"status",
		"created_at",
		"updated_at",
		"approved_at",
		"approved_by",
		"rejected_at",
		"rejected_by",
		"rejection_reason"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findByVeterinarianId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "veterinarian_id = $id")) return $registers;

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
		$data = $this->database->update($this->table, $data, $where,$and);
		return $this->getObject($data);
	}

	public function multipleTransaction(array $matriz): void {
		$this->database->multipleTransaction($matriz);
	}

	private function getObject(array $data){
		foreach ($data as $v){
			$this->registers[$v[$this->columns[0]]] = new VeterinarianApprovalPending(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(string) $v[$this->columns[8]],
				(int) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(int) $v[$this->columns[11]],
				(string) $v[$this->columns[12]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IVeterinarianApprovalPendingRepository {
	/**
	 * Summary of findByVeterinarianId
	 * @param int $id
	 * @return array|VeterinarianApprovalPending
	 */
	public function findByVeterinarianId(int $id);

	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|VeterinarianApprovalPending
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
