<?php
namespace App\Observers;

use App\Models\Errand;
use Faker\Provider\Uuid;

class ErrandObserver
{
    public function creating(Errand $errand){
        if (!$errand->id){
            $errand->id = Uuid::uuid();
        }
    }
}