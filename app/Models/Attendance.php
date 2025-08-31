<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'date', 'check_in', 'check_out', 'hours_worked'];

    // Calculate hours worked based on check-in and check-out times
    public function calculateHours()
    {
        if ($this->check_in && $this->check_out) {
            $checkInTime = Carbon::parse($this->check_in);
            $checkOutTime = Carbon::parse($this->check_out);
            $this->hours_worked = $checkInTime->diffInHours($checkOutTime);
        }
    }

    // Relation to Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
