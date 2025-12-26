<?php

namespace App\Services;

class ServicesCSV
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function readCSV(string $filename): array
    {
        $filePath = $this->projectDir. '/data/' .$filename;
        $file = fopen($filePath, 'r');

        $data = [];
        while (($row = fgetcsv($file, 1000, ";")) !== false) {
            $data[] = $row;
        }

        return $data;
    }

    public function reservHouse(int $id)
    {
        $filename = 'houses.csv';
        $house_data = $this->readCSV($filename);

        foreach($house_data as $house_param) {
            if ($house_param[0] == $id) {
                $house_param[6] = (int)$house_param[6] - 1;
                break;
            }
        }

        $filePath = $this->projectDir. '/data/' .$filename;
        $file = fopen($filePath, 'w');
        foreach($house_data as $row) {
            fputcsv($file, $row, ';');
        }
        fclose($file);
    }

    public function makeReservation(string $filename, array $data)
    {
        $filePath = $this->projectDir. '/data/' .$filename;
        $file = fopen($filePath, 'a+');

        fseek($file, 0);
        $rows = [];
        while (($row = fgetcsv($file)) !== false) {
            $rows[] = $row;
        }

        $id = 1;
        if (count($rows) >= 1) {
            $id = (int)end($rows)[0] + 1;
        }
        $data[0] = $id;
        $this->reservHouse($data[1]);

        fputcsv($file, $data, ';');

        fclose($file);
    }

    public function updateComment(string $filename, string $comment, int $id)
    {
        $data = $this->readCSV($filename);

        foreach($data as &$elem) {
            if ($elem[0] == $id) {
                $elem[3] = $comment;
                break;
            }
        }

        $filePath = $this->projectDir. '/data/' .$filename;
        $file = fopen($filePath, 'w');
        foreach($data as $row) {
            fputcsv($file, $row, ';');
        }
        fclose($file);
    }
}
