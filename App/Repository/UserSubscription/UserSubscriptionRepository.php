<?php

declare(strict_types=1);
namespace App\Repository\UserSubscription;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\UserSubscription;

class UserSubscriptionRepository implements IUserSubscriptionRepository {
	use Helper;

    /**
     * @var UserSubscription[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "user_subscription";
	private array $columns = [
		"id",
		"user_id",
		"plan_id",
		"status",
		"stripe_subscription_id",
		"current_period_start",
		"current_period_end",
		"cancel_at_period_end",
		"canceled_at",
		"trial_start",
		"trial_end",
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
		if(!$registers = $this->database->select("*", $this->table, "user_id = $id", "deleted_at IS NULL")) return $registers;

		return $this->getObject($registers);
	}

	public function findByPlanId(int $plan_id) {
		if(!$registers = $this->database->select("*", $this->table, "user_id = $plan_id")) return $registers;

		return $this->getObject($registers);
	}

	public function findByStripeSubscriptionId(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "stripe_subscription_id = $id", "deleted_at IS NULL", "", 1)) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new UserSubscription(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(int) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(bool) $v[$this->columns[7]],
				(string) $v[$this->columns[8]],
				(string) $v[$this->columns[9]],
				(string) $v[$this->columns[10]],
				(string) $v[$this->columns[11]],
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

interface IUserSubscriptionRepository {
	/**
	 * Summary of findByUserId
	 * @param int $id
	 * @return array|UserSubscription
	 */
	public function findByUserId(int $id);

	/**
	 * Summary of findByPlanId
	 * @param int $plan_id
	 * @return array|UserSubscription
	 */
	public function findByPlanId(int $plan_id);

	/**
	 * Summary of findByStripeSubscriptionId
	 * @param int $plan_id
	 * @return array|UserSubscription
	 */
	public function findByStripeSubscriptionId(int $id);

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
