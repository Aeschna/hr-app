<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;

    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->view('emails.employee.employee_added') // Ensure the view file name is correct
                    ->with([
                        'employeeName' => $this->employee->first_name . ' ' . $this->employee->last_name,
                        'employeeEmail' => $this->employee->email,
                        'employeePhone' => $this->employee->phone,
                        'companyName' => $this->employee->company ? $this->employee->company->name : 'No Company Assigned',
                    ]);
    }
}
