<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $reportDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalSalesAmount = null;

    #[ORM\Column]
    private ?int $totalOrders = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReportDate(): ?\DateTimeImmutable
    {
        return $this->reportDate;
    }

    public function setReportDate(\DateTimeImmutable $reportDate): static
    {
        $this->reportDate = $reportDate;

        return $this;
    }

    public function getTotalSalesAmount(): ?string
    {
        return $this->totalSalesAmount;
    }

    public function setTotalSalesAmount(string $totalSalesAmount): static
    {
        $this->totalSalesAmount = $totalSalesAmount;

        return $this;
    }

    public function getTotalOrders(): ?int
    {
        return $this->totalOrders;
    }

    public function setTotalOrders(int $totalOrders): static
    {
        $this->totalOrders = $totalOrders;

        return $this;
    }
}
