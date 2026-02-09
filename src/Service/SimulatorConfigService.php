<?php
// src/Service/SimulatorConfigService.php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class SimulatorConfigService
{
    private string $configFile;
    private array $configCache;

    public function __construct(string $projectDir)
    {
        $this->configFile = $projectDir . '/config/simulator_config.json';
        $this->configCache = $this->loadConfig();
    }

    public function getConfig(): array
    {
        return $this->configCache;
    }

    public function updateWeights(array $weights): void
    {
        $this->configCache['weights'] = $weights;
        $this->saveConfig($this->configCache);
    }

    public function resetConfig(): void
    {
        $defaultConfig = [
            'weights' => [
                'moyenne' => 40,
                'specialites' => 40,
                'preferences' => 20
            ]
        ];

        $this->saveConfig($defaultConfig);
        $this->configCache = $defaultConfig;
    }

    private function loadConfig(): array
    {
        $defaultConfig = [
            'weights' => [
                'moyenne' => 40,
                'specialites' => 40,
                'preferences' => 20
            ]
        ];

        if (!file_exists($this->configFile)) {
            return $defaultConfig;
        }

        $content = file_get_contents($this->configFile);
        $savedConfig = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $defaultConfig;
        }

        return array_merge($defaultConfig, $savedConfig);
    }

    private function saveConfig(array $config): void
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir(dirname($this->configFile));
        
        file_put_contents($this->configFile, json_encode($config, JSON_PRETTY_PRINT));
        $this->configCache = $config;
    }
}