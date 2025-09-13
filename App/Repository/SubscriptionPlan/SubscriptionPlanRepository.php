<?php

declare(strict_types=1);
namespace App\Repository\SubscriptionPlan;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\SubscriptionPlan;

class SubscriptionPlanRepository implements ISubscriptionPlanRepository {
	use Helper;

    /**
     * @var SubscriptionPlan[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "subscription_plan";
	private array $columns = [
		"id"
,		"name",
		"slug",
		"description",
		"price_monthly",
		"price_yearly",
		"features",
		"max_listings",
		"max_photos",
		"highlight_listings",
		"priority_support",
		"analytics_access",
		"stripe_price_id_monthly",
		"stripe_price_id_yearly",
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

	public function findAll() {
		if(!$registers = $this->database->select("*", $this->table, 'is_active = TRUE', 'deleted_at IS NULL')) return $registers;
		
		return $this->getObject($registers);
	}

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id")) return $registers;

		return $this->getObject($registers);
	}

	public function findByStripePriceId(int $id) {
		$sql = "SELECT * 
				FROM $this->table 
				WHERE stripe_price_id_monthly = $id 
					OR stripe_price_id_yearly = $id";
		if(!$registers = $this->database->runSelect($sql)) return $registers;

		return $this->getObject($registers);
	}

	public function findBySlug(string $slug) {
		if(!$registers = $this->database->select("*", $this->table, "slug = $slug", "deleted_at IS NULL")) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new SubscriptionPlan(
				(int) $v[$this->columns[0]],
				(string) $v[$this->columns[1]],
				(string) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(int) $v[$this->columns[4]],
				(int) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(int) $v[$this->columns[7]],
				(int) $v[$this->columns[8]],
				(bool) $v[$this->columns[9]],
				(bool) $v[$this->columns[10]],
				(bool) $v[$this->columns[11]],
				(string) $v[$this->columns[12]],
				(string) $v[$this->columns[13]],
				(bool) $v[$this->columns[14]],
				(string) $v[$this->columns[15]],
				(string) $v[$this->columns[16]],
				(string) $v[$this->columns[17]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface ISubscriptionPlanRepository {

	/**
	 * Summary of findAll
	 * @return array|SubscriptionPlan
	 */
	public function findAll();

	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|SubscriptionPlan
	 */
	public function findById(int $id);

	/**
	 * Summary of findByStripePriceId
	 * @param int $id
	 * @return array|SubscriptionPlan
	 */
	public function findByStripePriceId(int $id);

	/**
	 * Summary of findBySlug
	 * @param string $slug
	 * @return array|SubscriptionPlan
	 */
	public function findBySlug(string $slug);

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
