<?php
namespace App\Service;

class FooterService
{
    public function getButtons(): array
    {
        $iconDir = "assets/icons/footer/";
        return [
            ["route" => "dashboard", "label" => "Dashboard", "icon" => $iconDir . "icon1.svg"],
            ["route" => "stats", "label" => "Stats", "icon" => $iconDir . "icon2.svg"],
            ["route" => "scan", "label" => "Scan", "icon" => $iconDir . "icon3.svg"],
            ["route" => "vault", "label" => "Vault", "icon" => $iconDir . "icon4.svg"],
            ["route" => "search", "label" => "Search", "icon" => $iconDir . "icon5.svg"]
        ];
    }
}
