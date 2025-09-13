<?php 

declare(strict_types=1);

namespace App\Repository\Database;

use App\Model\Database;
use Exception;

class DatabaseRepository implements IDatabaseRepository {

    private Database $database;

    public function __construct(Database $database){
        $this->database = $database;
    }

    public function enableCommit(): void{
        $this->database->commit = true;
    }

    public function disableCommit(): void {
        $this->database->commit = false;
    }

    public function multipleTransaction(array $matriz): void {
        $this->database->multipleTransaction($matriz);
    }

    public function commit(): void {
        $this->database->connection->commit();
    }

    public function destruction(): void {
        $this->database->connection =  null;
    }
}

interface IDatabaseRepository {
    /**
     * Summary of enableCommit
     * @return void
     */
    public function enableCommit(): void;

    /**
     * Summary of disableCommit
     * @return void
     */
    public function disableCommit(): void;

    /**
     * Summary of multipleTransaction
     * @param array $matriz
     * @return void
     * @throws Exception
     */
    public function multipleTransaction(array $matriz): void;

    /**
     * Summary of commit
     * @return void
     */
    public function commit(): void;

    /**
     * Summary of destruction
     * @return void
     */
    public function destruction(): void;
}