<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Services\ServicesCSV;
use PHPUnit\Framework\TestCase;

class ServicesCSVTest extends TestCase
{
    private $servicesCSV;
    private $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/csv_test_' . uniqid();
        mkdir($this->tempDir);
        mkdir($this->tempDir . '/data');

        $this->servicesCSV = new ServicesCSV($this->tempDir);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function testReadCSVWithEmptyFile(): void
    {
        $csvContent = '';
        file_put_contents($this->tempDir . '/data/test.csv', $csvContent);

        $result = $this->servicesCSV->readCSV('test.csv');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testReadCSVWithData(): void
    {
        $csvContent = "1;Дом1;Удобства;2;1;100;1\n2;Дом2;Удобства;3;2;150;2";
        file_put_contents($this->tempDir . '/data/houses.csv', $csvContent);

        $result = $this->servicesCSV->readCSV('houses.csv');

        $this->assertCount(2, $result);
        $this->assertEquals(['1', 'Дом1', 'Удобства', '2', '1', '100', '1'], $result[0]);
        $this->assertEquals(['2', 'Дом2', 'Удобства', '3', '2', '150', '2'], $result[1]);
    }

    public function testReservHouse(): void
    {
        $csvContent = "1;Дом1;Удобства;2;1;100;2\n2;Дом2;Удобства;3;2;150;3";
        file_put_contents($this->tempDir . '/data/houses.csv', $csvContent);

        $this->servicesCSV->reservHouse(1);

        $updatedContent = file_get_contents($this->tempDir . '/data/houses.csv');
        $lines = explode("\n", trim($updatedContent));

        $updatedData = [];
        foreach ($lines as $line) {
            $updatedData[] = str_getcsv($line, ';');
        }

        $this->assertEquals('1', $updatedData[0][0]);
        $this->assertEquals('Дом1', $updatedData[0][1]);
        $this->assertEquals('1', $updatedData[0][6]);

        $this->assertEquals('3', $updatedData[1][6]);
    }

    public function testReservHouseWithNonExistentId(): void
    {
        $csvContent = "1;Дом1;Удобства;2;1;100;1\n2;Дом2;Удобства;3;2;150;2";
        $originalContent = $csvContent;
        file_put_contents($this->tempDir . '/data/houses.csv', $csvContent);

        $this->servicesCSV->reservHouse(999);

        $updatedContent = file_get_contents($this->tempDir . '/data/houses.csv');
        $this->assertEquals($originalContent, trim($updatedContent));
    }

    public function testMakeReservationWithEmptyFile(): void
    {
        file_put_contents($this->tempDir . '/data/reservations.csv', '');

        $housesContent = '1;Дом1;Удобства;2;1;100;2';
        file_put_contents($this->tempDir . '/data/houses.csv', $housesContent);

        $reservationData = [0, 1, '88005553535', 'Тестовый комментарий'];

        $this->servicesCSV->makeReservation('reservations.csv', $reservationData);

        $reservationsContent = file_get_contents($this->tempDir . '/data/reservations.csv');
        $reservationsLines = explode("\n", trim($reservationsContent));

        $this->assertCount(1, $reservationsLines);

        $reservation = str_getcsv($reservationsLines[0], ';');
        $this->assertEquals('1', $reservation[0]);
        $this->assertEquals('1', $reservation[1]);
        $this->assertEquals('88005553535', $reservation[2]);
        $this->assertEquals('Тестовый комментарий', $reservation[3]);

        $housesContent = file_get_contents($this->tempDir . '/data/houses.csv');
        $housesLines = explode("\n", trim($housesContent));
        $house = str_getcsv($housesLines[0], ';');
        $this->assertEquals('1', $house[6]);
    }

    public function testUpdateComment(): void
    {
        $reservationsContent = "1;1;88005553535;Комментарий1\n2;1;88005553536;Комментарий2";
        file_put_contents($this->tempDir . '/data/reservations.csv', $reservationsContent);

        $this->servicesCSV->updateComment('reservations.csv', 'Новый комментарий1', 1);

        $reservationsContent = file_get_contents($this->tempDir . '/data/reservations.csv');
        $reservationsLines = explode("\n", trim($reservationsContent));

        $updatedReservation = str_getcsv($reservationsLines[0], ';');
        $this->assertEquals('Новый комментарий1', $updatedReservation[3]);

        $secondReservation = str_getcsv($reservationsLines[1], ';');
        $this->assertEquals('Комментарий2', $secondReservation[3]);
    }
}
