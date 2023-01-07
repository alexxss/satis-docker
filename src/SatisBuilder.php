<?php


use Symfony\Component\Process\Process;

class SatisBuilder
{
    static public function build(?string $name = null, bool $withOutput = false): bool
    {
        $command = [
            'composer',
            'satis:build',
            'satis.json',
            '/build',
        ];
        if (!empty($name)) $command[] = $name;
        $process = new Process($command);
        $process->run(function ($type, $buffer) use ($withOutput) {
            if ($withOutput) {
                var_export($buffer); echo '<br>';
            }
        });
        return $process->isSuccessful();
    }
}