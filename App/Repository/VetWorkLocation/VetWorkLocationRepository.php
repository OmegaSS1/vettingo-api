<?php

declare(strict_types=1);
namespace App\Repository\VetWorkLocation;

use App\Traits\Helper;
use App\Model\Database;
use App\Model\VetWorkLocation;
use Exception;

class VetWorkLocationRepository implements IVetWorkLocationRepository {
	use Helper;

    /**
     * @var VetWorkLocation[]
     */
    private array $registers = [];
	private Database $database;
	private string $table = "vet_work_location";
	private array $columns = [
		"id",
		"veterinarian_id",
		"city_id",
		"address",
		"number",
		"complement",
		"neighborhood",
		"zip_code",
		"latitude",
		"longitude",
		"isActive",
		"created_at",
		"updated_at",
		"state_id",
		"name",
		"deleted_at"
	];

    /**
     * @param Database $db
     */
    public function __construct(Database $database){
        $this->database = $database;
    }

	public function findAnyVeterinarian(array $options) {
		[$and, $orderParams, $limit, $offset] = $this->database->mountOption($options);
		$and = !empty($orderParams) ? "AND $and" : "";
		$order = !empty($orderParams) ? "ORDER BY $orderParams" : "";
		$limit = $limit > 0 ? "LIMIT $limit" : "";
		$offset = "OFFSET $offset";

		$query = "with work as (
					select 
						vwl.veterinarian_id,
						concat(vwl.address, ', ', vwl.number, ', ', vwl.neighborhood, ' - ', vwl.zip_code) address,
						jsonb_agg(
							json_build_object(
								case
									when day_of_week = 1 then 'Segunda-Feira'
									when day_of_week = 2 then 'Terça-Feira'
									when day_of_week = 3 then 'Quarta-Feira'
									when day_of_week = 4 then 'Quinta-Feira'
									when day_of_week = 5 then 'Sexta-Feira'
									when day_of_week = 6 then 'Sabado-Feira'
									when day_of_week = 0 then 'Domingo-Feira'
								end,
								concat(start_time, ' - ', end_time)
							)
						) schedule
					from vet_work_location vwl 
					join vet_work_location_schedule schedule on schedule.vet_work_location_id = vwl.id
					where vwl.\"isActive\" = true	
					$and
					group by vwl.id, vwl.address
					$order
					$limit
					$offset
				) select 
					v.user_id id,
					CONCAT(u.first_name,' ', u.last_name) name,
					CONCAT(s.uf,'-',v.crmv) crmv,
					v.bio,
					v.website,
					v.avatar,
					v.emergencial_attendance emergencial,
					v.domiciliary_attendance domiciliary,
					count(work.veterinarian_id) totalWorkLocation
				from veterinarian v
				join public.user u on u.id = v.user_id 
				join state s on s.id = v.crmv_state_id 
				join work on work.veterinarian_id = v.id
				group by v.id, u.first_name, u.last_name, s.uf, v.bio, v.crmv,v.website, v.avatar, emergencial, domiciliary";
		return $this->database->runSelect($query);
	}

	public function findByVeterinarianId(int $id, array $options = []): array|VetWorkLocation {
		[$and, $orderParams] = $this->database->mountOption($options);
		if(!$registers = $this->database->select("*", $this->table, "veterinarian_id = $id", $and, $orderParams)) return $registers;

		return $this->getObject($registers);
	}

	public function findById(int $id) {
		if(!$registers = $this->database->select("*", $this->table, "id = $id", "\"isActive\" = TRUE AND deleted_at IS NULL")) return $registers;

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
			$this->registers[$v[$this->columns[0]]] = new VetWorkLocation(
				(int) $v[$this->columns[0]],
				(int) $v[$this->columns[1]],
				(int) $v[$this->columns[2]],
				(string) $v[$this->columns[3]],
				(string) $v[$this->columns[4]],
				(string) $v[$this->columns[5]],
				(string) $v[$this->columns[6]],
				(string) $v[$this->columns[7]],
				(float) $v[$this->columns[8]],
				(float) $v[$this->columns[9]],
				(bool) $v[$this->columns[10]],
				(string) $v[$this->columns[11]],
				(string) $v[$this->columns[12]],
				(int) $v[$this->columns[13]],
				(string) $v[$this->columns[14]],
				(string) $v[$this->columns[15]],
			);
		}

		if(count($data) == 1){
			return $this->registers[$data[0][$this->columns[0]]];
		}
		return array_values($this->registers);
	}
};

interface IVetWorkLocationRepository {
	/**
	 * Summary of findAnyVeterinarian
	 * @param array $options
	 * @return array|VetWorkLocation
	 */
	public function findAnyVeterinarian(array $options);

	/**
	 * Summary of findByVeterinarianId
	 * @param int $id
	 * @param array $options
	 * @return array|VetWorkLocation
	 */
	public function findByVeterinarianId(int $id, array $options);

	/**
	 * Summary of findById
	 * @param int $id
	 * @return array|VetWorkLocation
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
