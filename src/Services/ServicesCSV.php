final <?php

declare(strict_types=1);

namespace App\Services;

class ServicesCSV
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @return ((null|string)[]|null)[]
     *
     * @psalm-return list{0?: non-empty-list<null|string>|null,...}
     */
    public function readCSV(string $filename): array
    {
        $filePath = $this->projectDir. '/data/' .$filename;
        $file = fopen($filePath, 'r');

        $data = [];
        while (($row = fgetcsv($file, 1000, ';')) !== false) {
            $data[] = $row;
        }

        return $data;
    }

    public function reservHouse(int $id): void
    {
        $filename = 'houses.csv';
        $data = $this->readCSV($filename);

        foreach ($data as &$elem) {
            if ($elem[0] == $id) {
                $elem[6] = (int)$elem[6] - 1;
                break;
            }
        }

        $filePath = $this->projectDir. '/data/' .$filename;
        $file = fopen($filePath, 'w');
        foreach ($data as $row) {
            fputcsv($file, $row, ';');
        }
        fclose($file);
    }

    public function makeReservation(string $filename, array $data): void
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

    public function updateComment(string $filename, string $comment, int $id): void
    {
        $data = $this->readCSV($filename);

        foreach ($data as &$elem) {
            if ($elem[0] == $id) {
                $elem[3] = $comment;
                break;
            }
        }

        $filePath = $this->projectDir. '/data/' .$filename;
        $file = fopen($filePath, 'w');
        foreach ($data as $row) {
            fputcsv($file, $row, ';');
        }
        fclose($file);
    }
}
