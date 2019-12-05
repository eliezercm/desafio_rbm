<?php
namespace Src\App\Models;

class SaleGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT
                id, paymentId, customerName, total
            FROM
                sale;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT
                id, paymentId
            FROM
                sale
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert(String $paymentId, Array $input)
    {
        $statement = "
            INSERT INTO sale
                (paymentId, customerName, total)
            VALUES
                (:paymentId, :customerName, :total);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'paymentId' => $paymentId,
                'customerName'  => $input['customerName'],
                'total' => $input['paymentTotal']
            ));
            $lastSaleId = $this->db->lastInsertId();
            return $lastSaleId;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}