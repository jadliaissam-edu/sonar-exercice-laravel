<?php

namespace App\Level3\Services;

use PDO;

class ExportService
{
    public function countLines(string $fileName): string
    {
        $output = shell_exec('wc -l ' . $fileName);

        return trim((string) $output);
    }

    public function archive(string $name): int
    {
        exec('tar -czf /tmp/' . $name . '.tgz storage/app/uploads', $lines, $status);

        return $status;
    }

    public function publish(string $file): void
    {
        chmod($file, 0777);
    }

    public function connectLegacy(): PDO
    {
        return new PDO('mysql:host=192.168.1.20;dbname=helpdesk', 'root', '');
    }
}
