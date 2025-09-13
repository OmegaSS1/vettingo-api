<?php

declare(strict_types=1);
namespace App\Repository\VetWorkLocationSchedule;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\VetWorkLocationSchedule;

class VetWorkLocationScheduleRepository implements IVetWorkLocationScheduleRepository {
	use Helper;

    /**
     * @var VetWorkLocationSchedule[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "vet_work_location_schedule";
	private array $columns = [
		"id",
		"vet_work_location_id",
		"day_of_week",
		"start_time",
		"end_time",
		"isActive",
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

	public function findByVetWorkLocationId(int $id, array $options = []) {
		[$and, $orderParams] = $this->database->mountOption($options);

		if(!$registers = $this->database->select("*", $this->table, "vet_work_location_id = $id", $and, $orderParams)) return $registers;

		return $this->getObject($registers);
	}

	public function findExistingSchedule(int $id, int $dayOfWeek) {
		if(!$registers = $this->database->select("*", $this->table, "vet_work_location_id = $id", "day_of_week = $dayOfWeek")) return $registers;

		return $this->getObject($registers);
	}

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id", "\"isActive\" = TRUE")) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new VetWorkLocationSchedule(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(int) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(bool) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(string) $v[$this->columns[8]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IVetWorkLocationScheduleRepository {
	/**
	 * Summary of findByVetWorkLocationId
	 * @param int $id
	 * @param array $options
	 * @return array|VetWorkLocationSchedule
	 */
	public function findByVetWorkLocationId(int $id, array $options = []);

	/**
	 * Summary of findByVetWorkLocationId
	 * @param int $id
	 * @param int $dayOfWeek
	 * @return array|VetWorkLocationSchedule
	 */
	public function findExistingSchedule(int $id, int $dayOfWeek);

	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|VetWorkLocationSchedule
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
