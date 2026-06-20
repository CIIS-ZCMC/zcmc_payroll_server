<?php

namespace App\Contract;

use App\Models\NightDifferentialRules;
use Illuminate\Support\Collection;

interface NightDifferentialRuleInterface
{
   public function getAll(): Collection;
   public function updateOrCreate(array $data): NightDifferentialRules;
   public function show(int $id): NightDifferentialRules; 
   public function findByEmploymentType(string $employment_type): NightDifferentialRules;
}